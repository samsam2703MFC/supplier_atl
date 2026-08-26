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

    '2x35g'  -> opakowanie = 2 szt., waga laczna 70 g
    '100g'   -> opakowanie = 100 g,  waga 100 g
    ''       -> figura sprzedawana na sztuki ('pce'), waga nieznana
    """
    poids = clean(poids)

    m = re.fullmatch(r"(\d+)\s*[xX]\s*(\d+)\s*g", poids)
    if m:
        count, grams = int(m.group(1)), int(m.group(2))
        return count, "pcs", count * grams

    m = re.fullmatch(r"(\d+)\s*g", poids)
    if m:
        grams = int(m.group(1))
        return grams, "g", grams

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

        # Uklad pol jak w formularzu "Dodaj produkt" w panelu
        # (catalog.twig, saveProduct): API oczekuje "is_active", nie "active",
        # a puste pola ida jako null - klucz zawsze jest obecny.
        product = {
            "sku": sku,
            "name": name,
            "package_size": package_size,
            "package_unit": package_unit,
            "vat_rate": vat_rate,
            "is_active": 1,
            "weight_grams": weight_grams,
            "weight_unit": "g",
            "shelf_life_days": parse_shelf_life(dlc),
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
    dump("products_import.json", {"products": products}, len(products))
    dump("prices_by_sku.json", prices)
    dump("specifications_by_sku.json", specs)
    dump("allergens_by_sku.json", allergens)


if __name__ == "__main__":
    main()
