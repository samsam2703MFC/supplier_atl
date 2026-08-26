#!/usr/bin/env python3
"""
Parser cennika dostawcy CDT / Atelier by Berlo (Listing produits PRO 2026).

Zamienia PDF od dostawcy na pliki JSON gotowe do importu w panelu:
  - products_import.json      -> Katalog > "Importuj produkty"
  - prices_by_sku.json        -> dane wejsciowe dla build_price_list.py
  - specifications_by_sku.json-> sklad (POST /ajax/catalog/products/{id}/specification)
  - allergens_by_sku.json     -> wykryte alergeny (do recznego przypisania)

Uzycie:
    pip install pdfplumber
    python3 parse_pdf.py --pdf source/listing_produits_2026.pdf --vat 0.06
"""

import argparse
import json
import re
from pathlib import Path

import pdfplumber

# "12 mois" -> 360 dni (miesiac liczony jako 30 dni; konwencja zachowawcza)
DAYS_PER_MONTH = 30

# Alergeny sa w PDF pisane WERSALIKAMI - to konwencja etykietowania UE.
ALLERGEN_PATTERNS = [
    ("LAIT", "milk"),
    ("BLÉ", "gluten_wheat"),
    ("AVOINE", "gluten_oats"),
    ("ORGE", "gluten_barley"),
    ("SOJA", "soy"),
    ("AMANDES", "nuts_almond"),
    ("NOISETTES", "nuts_hazelnut"),
    ("NOIX DU BRESIL", "nuts_brazil"),
    ("FRUITS A COQUE", "nuts"),
    ("SESAME", "sesame"),
    ("dioxyde de soufre", "sulphites"),
]

# Slownik alergenow w panelu jest gruboziarnisty (z eksportu produktow:
# cereals_gluten / eggs / milk / nuts). Mapujemy na niego kody z parsera.
# soy, sesame i sulphites nie wystapily w probce eksportu - ich kodow nie
# potwierdzono, wiec ida tylko do allergens_text, nie do listy `allergens`.
ALLERGEN_DICT = {
    "milk":          ("milk", "mleko"),
    "gluten_wheat":  ("cereals_gluten", "zboża zawierające gluten"),
    "gluten_oats":   ("cereals_gluten", "zboża zawierające gluten"),
    "gluten_barley": ("cereals_gluten", "zboża zawierające gluten"),
    "nuts_almond":   ("nuts", "orzechy"),
    "nuts_hazelnut": ("nuts", "orzechy"),
    "nuts_brazil":   ("nuts", "orzechy"),
    "nuts":          ("nuts", "orzechy"),
}
UNCONFIRMED_CODES = {"soy", "sesame", "sulphites"}

# Literowka w PDF dostawcy - poprawiona swiadomie, patrz README.
NAME_FIXES = {
    "MUESLI-L": ("Tuiles muesli Chocolat Lair", "Tuiles muesli Chocolat Lait"),
}


def clean(text):
    return re.sub(r"\s+", " ", (text or "").replace("\n", " ")).strip()


def parse_price(raw):
    """'3,36' -> 3.36"""
    value = clean(raw).replace(" ", "").replace(" ", "").replace(",", ".")
    return round(float(value), 2)


def parse_package(poids, carton):
    """
    Zwraca (package_size, package_unit, weight_grams).

    package_size to liczba sztuk w KARTONIE (kolumna Carton), nie rozmiar
    pojedynczego opakowania. Tak dziala panel: w eksporcie produktow
    package_size = 8 dla tarty 22cm, a w cenniku ogolnym ten sam SKU ma
    pack_quantity_in_base_unit = 8. weight_grams to waga JEDNEJ sztuki
    (tam 1200 g za tarte, nie za osiem).

    Kolumna Poids sluzy wiec tylko do wagi:
      '2x35g' -> 70 g (sasiet z dwiema cuillers)
      '100g'  -> 100 g
      ''      -> figura na sztuki ('pce'), waga nieznana
    """
    poids = clean(poids)
    per_carton = parse_carton(carton)
    package_size = per_carton if per_carton else 1

    m = re.fullmatch(r"(\d+)\s*[xX]\s*(\d+)\s*g", poids)
    if m:
        count, grams = int(m.group(1)), int(m.group(2))
        return package_size, "pcs", count * grams

    m = re.fullmatch(r"(\d+)\s*g", poids)
    if m:
        return package_size, "pcs", int(m.group(1))

    if not poids and "pce" in clean(carton).lower():
        return 1, "pcs", None

    raise ValueError(f"Nieznany format pola Poids: {poids!r} (carton={carton!r})")


def parse_shelf_life(dlc):
    """'12 mois' -> 360"""
    m = re.search(r"(\d+)\s*mois", clean(dlc), re.IGNORECASE)
    if not m:
        raise ValueError(f"Nieznany format DLC/DLUO: {dlc!r}")
    return int(m.group(1)) * DAYS_PER_MONTH


def parse_carton(carton):
    """'25' -> 25 ; 'pce' -> None (sprzedaz na sztuki)"""
    carton = clean(carton)
    return int(carton) if carton.isdigit() else None


def detect_allergens(ingredients):
    found = []
    for needle, code in ALLERGEN_PATTERNS:
        if needle.lower() in ingredients.lower():
            found.append(code)
    return sorted(set(found))


def extract_rows(pdf_path):
    """Zwraca liste wierszy produktowych z sekcja (year_round / holiday)."""
    rows = []
    section = "year_round"

    with pdfplumber.open(pdf_path) as pdf:
        for page in pdf.pages:
            for table in page.extract_tables():
                for raw in table:
                    cells = [clean(c) for c in raw]
                    if len(cells) < 7:
                        continue
                    if cells[0] == "Référence":
                        # Drugi naglowek = poczatek sekcji swiatecznej
                        # ("Pour les fêtes de fin d'année")
                        if rows:
                            section = "holiday"
                        continue
                    if not cells[1]:  # pusty wiersz / artefakt lamania strony
                        continue
                    rows.append((cells, section))
    return rows


def normalize_sku(sku, separator):
    """Zamienia myslnik w referencji dostawcy (SPOON-N) na wybrany separator.

    Referencje w PDF maja myslniki; jesli API ich nie przyjmuje, separator "_"
    daje SPOON_N, a "" daje SPOONN. Zmiana dotyczy wszystkich plikow naraz,
    bo cennik, sklad i alergeny sa kluczowane po SKU.
    """
    return sku.replace("-", separator)


def build(pdf_path, vat_rate, sku_separator="_"):
    products, prices, specs, allergens = [], [], {}, {}

    for cells, section in extract_rows(pdf_path):
        name, sku, poids, price, carton, ingredients, dlc = cells[:7]

        if sku in NAME_FIXES:
            wrong, right = NAME_FIXES[sku]
            if name == wrong:
                name = right

        sku = normalize_sku(sku, sku_separator)

        package_size, package_unit, weight_grams = parse_package(poids, carton)

        # Uklad pol jak w eksporcie produktow z panelu (products_export_*.json):
        # to jest format, ktory system rozumie. `is_active` dokladamy obok
        # `active`, bo formularz "Dodaj produkt" (catalog.twig, saveProduct)
        # wysyla wlasnie ta nazwe - nadmiarowy klucz jest ignorowany, brakujacy
        # nie. category_id pomijamy: panel dostawcy nie ma slownika kategorii
        # katalogu (jest tylko /ajax/raw-material-categories).
        found = detect_allergens(ingredients)
        seen, allergen_list = set(), []
        for code in found:
            if code in ALLERGEN_DICT:
                dict_code, dict_name = ALLERGEN_DICT[code]
                if dict_code not in seen:
                    seen.add(dict_code)
                    allergen_list.append({"code": dict_code, "name": dict_name})

        product = {
            "sku": sku,
            "name": name,
            "active": True,
            "is_active": 1,
            "weight_grams": weight_grams,
            "weight_unit": "g",
            "package_size": package_size,
            "package_unit": package_unit,
            "vat_rate": vat_rate,
            "shelf_life_days": parse_shelf_life(dlc),
            "storage_info": None,
            "preparation_info": None,
            "composition": clean(ingredients) or None,
            "allergens": allergen_list,
            "allergens_text": ", ".join(a["name"] for a in allergen_list),
        }
        products.append(product)

        prices.append({
            "sku": sku,
            "name": name,
            "price_net": parse_price(price),
            "carton_qty": parse_carton(carton),
            "section": section,
            "source_poids": poids or None,
        })

        specs[sku] = {
            "lang_code": "fr",
            "composition": ingredients,
            "storage_info": None,
            "preparation_info": None,
            "dlc_dluo": dlc,
        }
        allergens[sku] = detect_allergens(ingredients)

    return products, prices, specs, allergens


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--pdf", default="source/listing_produits_2026.pdf")
    ap.add_argument("--vat", default="0.06",
                    help="Stawka VAT jako ulamek dziesietny, np. 0.06 lub 0.23")
    ap.add_argument("--out", default=".")
    ap.add_argument("--sku-separator", default="_",
                    help='Czym zastapic myslnik w SKU: "_" (domyslnie), "" '
                         'albo dowolny inny znak')
    args = ap.parse_args()

    out = Path(args.out)
    products, prices, specs, allergens = build(args.pdf, args.vat, args.sku_separator)

    def dump(filename, payload, count=None):
        path = out / filename
        path.write_text(
            json.dumps(payload, ensure_ascii=False, indent=2) + "\n",
            encoding="utf-8",
        )
        print(f"  {path}  ({count if count is not None else len(payload)} pozycji)")

    print(f"Sparsowano {len(products)} produktow z {args.pdf}")
    # API odrzuca gola tablice bledem INVALID_JSON_STRUCTURE - lista musi byc
    # opakowana w {"products": [...]}, tak samo jak w eksporcie z panelu.
    dump("products_import.json",
         {"export_info": {"language": "fr",
                          "language_name": "Français",
                          "total_products": len(products)},
          "products": products},
         len(products))
    dump("prices_by_sku.json", prices)
    dump("specifications_by_sku.json", specs)
    dump("allergens_by_sku.json", allergens)


if __name__ == "__main__":
    main()
