# CDT price list 2026 — what to do

Everything needed to get the CDT / Atelier by Berlo PRO 2026 listing
(25 products) into the panel. Source: `source/listing_produits_2026.pdf`.

**Done already:** PDF parsed, all files generated and validated.
**Left to do:** steps 1–5 below (you), plus the questions for CDT in §7.

Locked-in decisions: VAT `0.06` · prices in EUR, not converted · price list
applies to **all shops**.

---

## 1. Log in as CDT

The import files carry no supplier field — the panel takes `supplier_id` from
your session. Whoever is logged in **owns the imported products**. Log in as
CDT before step 2.

## 2. Import the products

Katalog → **„Import produktów"** → „Wybierz plik JSON" → `products_import.json`
→ „Rozpocznij import".

- the file is `{"products": [ … 25 items … ]}` — a **bare JSON array is
  rejected** by the API with `INVALID_JSON_STRUCTURE`, even though the panel's
  import handler sends one (`catalog.twig`, the `Array.isArray` branch). Its
  own comment records the same problem for the single-item case
- each item: `sku`, `name`, `package_size`, `package_unit`, `vat_rate`,
  `is_active` (**not** `active` — see `saveProduct` in `catalog.twig`),
  `weight_grams` (`null` where the PDF gives no weight), `weight_unit`,
  `shelf_life_days`
- SKUs use `_`, not `-` (`SPOON_N`). `parse_pdf.py --sku-separator` changes it
  across all four files at once; the PDF's own refs are hyphenated
- `package_size` is the **carton quantity**, currently **10 for every
  product**, set with `parse_pdf.py --carton-size 10`. This overrides the PDF,
  which lists 20 for the two SPOON items, 25 for the rest and `pce` for the
  eight figures — confirm the agreed carton with CDT. Drop the flag to fall
  back to the printed values. It is not the size of one sachet. That is how the
  panel models it — in its own export a 22 cm tart has `package_size` 8 and the
  same SKU carries `pack_quantity_in_base_unit` 8 in the cennik. The two must
  agree, or the price is read against the wrong quantity
- `weight_grams` is the weight of **one unit** (70 g for a 2×35 g sachet,
  100 g, `null` for the figures) — the tart above is 1200 g for one, not eight
- accepted: `.json` only, max 10 MB
- the grid reloads on success — confirm you see 25 new items

- [ ] 25 products visible in the catalog

## 3. Pull the catalog IDs

The price import keys on `product_id` (internal catalog ID), **not SKU** — so
the IDs only exist after step 2. Logged into the panel, browser console:

```js
fetch('/supplier/ajax/catalog/products').then(r => r.json()).then(j => console.log(JSON.stringify(j)))
```

Save the output as `catalog.json` next to `build_price_list.py`.

- [ ] `catalog.json` saved

## 4. Build the price file

```bash
python3 parse_pdf.py --carton-size 10          # products + source prices
python3 build_price_list.py --pricing carton   # price file, EUR 1040.90
```

`--pricing carton` multiplies the PDF's P.U. HTVA by the carton quantity, so
`price_list_import.carton.json` carries the price of a full carton and
`pack_quantity_in_base_unit` says how many units that is. Both numbers come
from the same `--carton-size`, so they cannot drift apart.

Writes `price_list_import.all-shops.json` → `{"prices": [{product_id, price_net}]}`.
The script prints a warning naming any SKU it could not match — if it does,
that product did not import in step 2. Do not import a partial file; fix step 2
and rebuild.

Season split, if you prefer to stage them (§7 — holiday items are ordered in
October for November delivery):

```bash
python3 build_price_list.py --catalog catalog.json --scope all-shops --section year_round
python3 build_price_list.py --catalog catalog.json --scope all-shops --section holiday
```

- [ ] file built, 25 items (or 15 + 10), no unmatched SKUs

## 5. Import the price list

Cennik → **„Importuj cennik"** → pick the effective date → select the file →
„Rozpocznij import".

- the effective date is chosen **in the modal**, not in the file, and must not
  be in the past — the panel rejects any date **before today**
  (`src/app/Views/price_list/index.twig`). Today itself passes the panel's own
  check, but the upstream API may still want a future date, so pick tomorrow or
  later to be safe
- prices apply to every shop at once
- page reloads on success

- [ ] prices visible in Cennik, correct effective date

---

## 6. Optional extras

Neither is an import file — both need product IDs from step 3, one call per
product.

**Ingredients** (`specifications_by_sku.json`) — French text straight from the
PDF, ready for `POST /ajax/catalog/products/{id}/specification`:

```json
{ "lang_code": "fr", "composition": "Chocolat Noir Origine (fèves de cacao, ...)" }
```

**Allergens** (`allergens_by_sku.json`) — detected from the uppercase names in
the ingredients. Assignment needs IDs from your allergen dictionary, so treat
this as a worksheet, not a payload.

⚠️ The PDF carries a blanket cross-contamination clause covering **every**
product, including ones showing no allergens:

> Fabriqué dans un atelier où se trouve du LAIT, de la farine de BLÉ (gluten),
> du SESAME, tous les FRUITS A COQUE, de la lécithine de SOJA.

Also: GUIMAU-D, OURSON-N and OURSON-L contain **pork gelatine**.

## 7. Ask CDT before going live

1. **Shelf lives look swapped** — SAPIN-N is 8 months while SAPIN-L and SAPIN-B
   are 12. St Nicolas runs the other way (blanc 8, noir/lait 12).
2. **SAPIN-L "Chocolat Lait" lists white-chocolate ingredients** — copy-paste
   slip in the PDF, and it changes the allergen answer.
3. **No weights for the 8 figures** (STNIC-*, SAPIN-*) — the Poids column is
   empty, so `weight_grams` is unset on those.
4. Confirm **6% VAT** is right for your market, and that prices stay in EUR.

Both errors are transcribed **as printed** — nothing was silently corrected.
One typo was fixed: MUESLI-L "Chocolat **Lair**" → "**Lait**".

## 8. Where these files live

Merged into `samsam2703MFC/supplier_atl` under `imports/cdt_berlo_2026/`, so
nothing needs to be applied from a bundle any more. The three original commits
are preserved in the history behind the merge.

Everything here was re-checked against the panel code in this repo before the
merge — see the endpoint table below; `parse_pdf.py` regenerates all four JSON
files byte-identically from the PDF.

---

## Endpoints

`supplierId` is filled in by the panel from your session — never send it.

### Products

| Panel (browser) | Upstream API | Payload |
|---|---|---|
| `GET /ajax/catalog/products` | `GET /material-suppliers/{supplierId}/catalog/products` | — |
| `POST /ajax/catalog/products/import` | `POST /material-suppliers/{supplierId}/catalog/products/import` | array of products (single object if one) |
| `POST /ajax/catalog/products` | `POST /material-suppliers/catalog/products` | one product |
| `PUT /ajax/catalog/products/{id}` | `PATCH /material-suppliers/catalog/products/{id}` | changed fields |
| `DELETE /ajax/catalog/products/{id}` | `DELETE /material-suppliers/catalog/products/{id}` | — |
| `POST /ajax/catalog/products/{id}/specification` | `POST /material-suppliers/catalog/products/{id}/specification` | `lang_code`, `composition`, `storage_info`, `preparation_info` |
| `POST /ajax/catalog/products/{id}/allergens` | `POST /material-suppliers/catalog/products/{id}/allergens` | allergen ID(s) |
| `DELETE /ajax/catalog/products/{id}/allergens/{allergenId}` | same shape | — |
| `POST /ajax/catalog/products/{id}/photo` | multipart upload | image |

### Prices — all shops (what you're using)

| Panel (browser) | Upstream API | Payload |
|---|---|---|
| `POST /ajax/price-list/import/all-shops` | `POST /material-suppliers/{supplierId}/price-lists/import/all-shops` | `{"prices": [{product_id, price_net}], "valid_from": "YYYY-MM-DD"}` |
| `POST /ajax/products/{productId}/prices/all-shops` | `POST /material-suppliers/{supplierId}/products/{productId}/price/all-shops` | one price |
| `DELETE /ajax/products/{productId}/prices/scheduled/{validFrom}/all-shops` | `DELETE /material-suppliers/{supplierId}/products/{productId}/price-lists/scheduled/{validFrom}` | — |

`valid_from` is added at the **top level** by the modal, not per item — that is
why `build_price_list.py --scope all-shops` leaves it out of the file.

### Prices — one client

| Panel (browser) | Upstream API | Payload |
|---|---|---|
| `POST /ajax/clients/{clientId}/price-list/import` | `POST /material-suppliers/{supplierId}/shops/{clientId}/price-lists/import` | `{"prices": [{product_id, price_net, valid_from}]}` |
| `POST /ajax/clients/{clientId}/products/{productId}/prices` | `POST /material-suppliers/{supplierId}/shops/{clientId}/products/{productId}/price` | one price |
| `DELETE /ajax/clients/{clientId}/products/{productId}/prices/{priceId}` | `DELETE /material-suppliers/{supplierId}/shops/{clientId}/price-lists/{priceId}` | — |

Here `valid_from` goes **per item, in the file** — the client modal has no date
picker.

### Pages

`/catalog` · `/price-list` · `/clients` · `/clients/{clientId}/price-list`

---

## Files

| File | Purpose |
|---|---|
| `products_import.json` | step 2 — the catalog import file |
| `prices_by_sku.json` | source prices (SKU, net price, carton qty, season) |
| `build_price_list.py` | step 4 — maps SKU → product_id |
| `specifications_by_sku.json` | ingredients per SKU |
| `allergens_by_sku.json` | detected allergens per SKU |
| `parse_pdf.py` | regenerates all of the above from the PDF |
| `source/listing_produits_2026.pdf` | the supplier's original listing |
| `README.md` | mapping decisions in detail |

Regenerate everything (e.g. at a different VAT rate):

```bash
pip install pdfplumber
python3 parse_pdf.py --vat 0.23
```

## If something fails

| Symptom | Cause |
|---|---|
| „Nieprawidłowy format pliku" | not `.json` — check the extension |
| „Nieprawidłowy plik JSON" | file edited by hand and broken; re-run the script |
| `INVALID_JSON_STRUCTURE` in the response | the payload is a bare array — it must be `{"products": [...]}` |
| the modal's generic „format" error | it shows that for *every* failure; read the real cause in F12 → Network → the `import` request → Response |
| date rejected on price import | `valid_from` is before today — pick a future date |
| script warns about unmatched SKUs | those products are not in the catalog — redo step 2 |
| prices import but show against wrong products | `catalog.json` is stale — re-pull it (step 3) |
