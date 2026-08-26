# Cennik dostawcy CDT — Atelier by Berlo, listing PRO 2026

Zrodlo: `source/listing_produits_2026.pdf`
("Listing produits 2026 – ATELIER BY BERLO X CDT", 3 strony, 25 produktow).

Ceny w PDF sa **netto (P.U. HTVA)** i podane w **EUR**.

## Pliki

| Plik | Do czego |
|---|---|
| `products_import.json` | Katalog → **Importuj produkty** (`POST /ajax/catalog/products/import`) |
| `prices_by_sku.json` | dane zrodlowe cen (SKU, cena netto, karton, sekcja) |
| `specifications_by_sku.json` | sklad z PDF (`POST /ajax/catalog/products/{id}/specification`) |
| `allergens_by_sku.json` | alergeny wykryte w skladzie (do przypisania recznie) |
| `parse_pdf.py` | parser PDF → powyzsze pliki JSON |
| `build_price_list.py` | mapuje SKU → `product_id` i buduje plik cennika |

## Kolejnosc importu

Import cennika operuje na `product_id` (wewnetrzne ID katalogu), nie na SKU —
dlatego **najpierw produkty, potem ceny**.

1. **Produkty** — Katalog → „Importuj produkty" → `products_import.json`.
2. **Pobierz ID z katalogu** — zalogowany w panelu, w konsoli przegladarki:
   ```js
   fetch('/ajax/catalog/products').then(r => r.json()).then(j => console.log(JSON.stringify(j)))
   ```
   Zapisz wynik jako `catalog.json`.
3. **Zbuduj cennik**:
   ```bash
   # dla wszystkich sklepow (date obowiazywania wybiera sie w modalu):
   python3 build_price_list.py --catalog catalog.json --scope all-shops

   # dla jednego klienta (data obowiazywania w pliku):
   python3 build_price_list.py --catalog catalog.json --scope client --valid-from 2026-10-01
   ```
4. **Import cennika** — Cennik → „Importuj cennik" (wszystkie sklepy) albo
   Klienci → Cennik → „Importuj cennik" (jeden klient).

Sekcje mozna importowac osobno: `--section year_round` / `--section holiday`.

## Decyzje przy mapowaniu — do weryfikacji

1. **VAT — zgadywany.** PDF podaje wylacznie ceny HTVA, bez stawki.
   Pliki wygenerowano z `vat_rate: "0.06"` (belgijska stawka na zywnosc, zgodna
   z rynkiem dostawcy). Jesli obowiazuje inna (np. 0.23 w PL), przegeneruj:
   `python3 parse_pdf.py --vat 0.23`.
2. **Waluta.** Panel nie ma pola waluty — ceny zapisza sie jako liczby.
   Wartosci z PDF to EUR; jesli panel pracuje w PLN, ceny wymagaja przeliczenia
   **przed** importem.
3. **`2x35g` (SPOON-N, SPOON-E)** → `package_size: 2`, `package_unit: "pcs"`,
   `weight_grams: 70`. Alternatywa: `70 g` jako rozmiar opakowania.
4. **Figury na sztuki** (STNIC-*, SAPIN-*) — kolumna Poids pusta, karton `pce`
   → `package_size: 1`, `package_unit: "pcs"`, **bez `weight_grams`**.
   Wagi trzeba dopytac u dostawcy.
5. **DLC/DLUO** — miesiac liczony jako 30 dni: „12 mois" → 360,
   „8 mois" → 240 (`DAYS_PER_MONTH` w `parse_pdf.py`).
6. **Ilosc w kartonie** (20 / 25 szt.) nie ma odpowiednika w modelu produktu —
   zachowana tylko w `prices_by_sku.json`.
7. **Literowka dostawcy poprawiona:** MUESLI-L „Chocolat **Lair**" → „Chocolat
   **Lait**" (`NAME_FIXES` w `parse_pdf.py`).

## Bledy w PDF dostawcy — do potwierdzenia

Przepisane **tak, jak w zrodle** (nie poprawiane):

- **SAPIN-N** ma DLC 8 mies., a SAPIN-L i SAPIN-B po 12 mies. Przy STNIC jest
  odwrotnie (blanc 8, noir/lait 12) — wyglada na zamienione wartosci.
- **SAPIN-L** („Chocolat Lait") ma w skladzie wpisany *Chocolat Blanc* —
  najpewniej kopiuj-wklej w PDF. Wplywa to na alergeny.

## Alergeny

`allergens_by_sku.json` zawiera alergeny wykryte w skladzie (w PDF pisane
WERSALIKAMI, zgodnie z konwencja UE). Przypisanie w panelu wymaga ID ze
slownika alergenow (`POST /ajax/catalog/products/{id}/allergens`), wiec plik
jest materialem pomocniczym, nie plikiem importu.

Dodatkowo PDF zawiera zbiorcza klauzule o zanieczyszczeniach krzyzowych —
dotyczy **wszystkich** pozycji, takze tych z pusta lista:

> Fabrique dans un atelier ou se trouve du LAIT, de la farine de BLE (gluten),
> du SESAME, tous les FRUITS A COQUE, de la lecithine de SOJA.

Uwaga: GUIMAU-D, OURSON-N i OURSON-L zawieraja **zelatyne wieprzowa**.

## Warunki handlowe z PDF (poza importem)

- Franco de port: **500 EUR HTVA**.
- Asortyment caloroczny: realizacja **15 dni**.
- Asortyment swiateczny (`section: holiday`, 10 pozycji): **zamowienie na
  poczatku pazdziernika, dostawa na poczatek listopada**.
