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

- 25 products, each with `sku`, `name`, `package_size`, `package_unit`,
  `vat_rate`, `weight_grams`, `weight_unit`, `shelf_life_days`, `active: 1`
- accepted: `.json` only, max 10 MB
- the grid reloads on success — confirm you see 25 new items

- [ ] 25 products visible in the catalog

## 3. Pull the catalog IDs

The price import keys on `product_id` (internal catalog ID), **not SKU** — so
the IDs only exist after step 2. Logged into the panel, browser console:

```js
fetch('/ajax/catalog/products').then(r => r.json()).then(j => console.log(JSON.stringify(j)))
```

Save the output as `catalog.json` next to `build_price_list.py`.

- [ ] `catalog.json` saved

## 4. Build the price file

```bash
python3 build_price_list.py --catalog catalog.json --scope all-shops
```

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

- the effective date is chosen **in the modal**, not in the file, and **must be
  in the future** — the panel rejects today or earlier
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

## 8. Push the branch (blocked on access)

Two commits sit on `claude/supplier-products-pricing-61zr1e`. Pushing is
refused — the Claude GitHub App has read but not write on this repo:

> Claude doesn't have GitHub access to ThaiZu/TFB-Supplier for your organization.

Either grant access — an org admin installs the app at
https://github.com/apps/claude/installations/select_target, or re-link from
https://claude.ai/customize/connectors?auth_start=github&auth_start_force=1 —
or push it yourself from the bundle, applied on top of `master` at `9b6c2b8`:

```bash
git fetch /path/to/cdt-import.bundle claude/supplier-products-pricing-61zr1e:claude/supplier-products-pricing-61zr1e
git push -u origin claude/supplier-products-pricing-61zr1e
```

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
| date rejected on price import | `valid_from` must be a **future** date |
| script warns about unmatched SKUs | those products are not in the catalog — redo step 2 |
| prices import but show against wrong products | `catalog.json` is stale — re-pull it (step 3) |
