// CDT / Atelier by Berlo 2026 — build the price-list import file.
//
// Run AFTER products_import.json has been imported (Katalog → „Import produktow"),
// logged into the panel, on any panel page. Paste the whole thing into the
// browser console and press Enter.
//
// It reads the catalog, matches your 25 SKUs to their product_id, and downloads
// price_list_import.all-shops.json ready for Cennik → „Importuj cennik".
//
// Optional: set SECTION to "year_round" or "holiday" to stage the seasons
// separately. null = all 25 in one file.
const SECTION = null;

// [sku, price_net (EUR, HTVA), section]
const CDT_PRICES = [
  ["SPOON-N", 2.95, "year_round"],
  ["SPOON-E", 2.95, "year_round"],
  ["MATINE-N", 3.36, "year_round"],
  ["MATINE-L", 3.36, "year_round"],
  ["MASSEP-D", 3.68, "year_round"],
  ["GUIMAU-D", 3.82, "year_round"],
  ["GRANOL-N", 3.29, "year_round"],
  ["GRANOL-L", 3.29, "year_round"],
  ["MUESLI-N", 3.29, "year_round"],
  ["MUESLI-L", 3.29, "year_round"],
  ["SABLE-N", 4.03, "year_round"],
  ["BALLS-N-N", 4.21, "year_round"],
  ["BALLS-N-L", 4.21, "year_round"],
  ["BALLS-A-N", 4.21, "year_round"],
  ["BALLS-A-L", 4.21, "year_round"],
  ["OURSON-N", 3.82, "holiday"],
  ["OURSON-L", 3.82, "holiday"],
  ["STNIC-N", 5.2, "holiday"],
  ["STNIC-L", 5.2, "holiday"],
  ["STNIC-B", 5.2, "holiday"],
  ["SAPIN-N", 4.8, "holiday"],
  ["SAPIN-L", 4.8, "holiday"],
  ["SAPIN-B", 4.8, "holiday"],
  ["SAPIN-F-N", 6.15, "holiday"],
  ["SAPIN-F-L", 6.15, "holiday"]
];

(async () => {
  const res = await fetch('/supplier/ajax/catalog/products', { headers: { 'Accept': 'application/json' } });
  if (!res.ok) throw new Error('Catalog fetch failed: HTTP ' + res.status);
  const raw = await res.json();

  // The endpoint may answer {data:[...]}, {products:[...]} or a bare array.
  const list = Array.isArray(raw) ? raw
             : Array.isArray(raw.data) ? raw.data
             : Array.isArray(raw.products) ? raw.products
             : null;
  if (!list) throw new Error('Unexpected catalog shape: ' + JSON.stringify(raw).slice(0, 200));

  const bySku = new Map();
  for (const p of list) {
    if (p && p.sku != null && p.id != null) bySku.set(String(p.sku).trim(), Number(p.id));
  }
  console.log('catalog products seen: ' + bySku.size);

  const wanted = SECTION ? CDT_PRICES.filter(r => r[2] === SECTION) : CDT_PRICES;
  const prices = [];
  const missing = [];
  for (const [sku, priceNet] of wanted) {
    const id = bySku.get(sku);
    if (id === undefined) { missing.push(sku); continue; }
    prices.push({ product_id: id, price_net: priceNet });
  }

  if (missing.length) {
    console.warn('NOT IN CATALOG (' + missing.length + '): ' + missing.join(', '));
    console.warn('Those products did not import. Fix the product import and re-run — do not import a partial price file.');
    return;
  }

  console.log('matched ' + prices.length + ' of ' + wanted.length + ' — downloading');
  const name = 'price_list_import.all-shops' + (SECTION ? '.' + SECTION : '') + '.json';
  const blob = new Blob([JSON.stringify({ prices }, null, 2) + '\n'], { type: 'application/json' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url; a.download = name;
  document.body.appendChild(a); a.click(); a.remove();
  setTimeout(() => URL.revokeObjectURL(url), 1000);
  console.log('saved ' + name);
})();
