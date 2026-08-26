// CDT import - diagnostyka. Pokazuje PRAWDZIWY blad API zamiast komunikatu
// "L'importation a echoue. Veuillez verifier le format du fichier." z modala.
//
// Modal pokazuje ten sam tekst przy kazdym bledzie, bo szuka pola "message",
// a API moze zwracac opis pod innym kluczem. Ten skrypt wypisuje surowe cialo
// odpowiedzi, wiec widac dokladnie, ktore pole jest odrzucane.
//
// Zalogowany w panelu (jako CDT), dowolna strona panelu -> konsola -> wklej.
// Nic nie tworzy, dopoki nie ustawisz TRY_SINGLE_CREATE = true.

const TRY_SINGLE_CREATE = false;   // true = utworzy 1 prawdziwy produkt

const PRODUCTS = [
  {
    "sku": "SPOON_N",
    "name": "Cuiller Chocolat chaud Nature",
    "package_size": 2,
    "package_unit": "pcs",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": 70,
    "weight_unit": "g",
    "shelf_life_days": 360
  },
  {
    "sku": "SPOON_E",
    "name": "Cuiller Chocolat chaud Épices de Nowel",
    "package_size": 2,
    "package_unit": "pcs",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": 70,
    "weight_unit": "g",
    "shelf_life_days": 360
  },
  {
    "sku": "MATINE_N",
    "name": "Matinettes Chocolat Noir Origine",
    "package_size": 100,
    "package_unit": "g",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": 100,
    "weight_unit": "g",
    "shelf_life_days": 360
  },
  {
    "sku": "MATINE_L",
    "name": "Matinettes Chocolat Lait Origine",
    "package_size": 100,
    "package_unit": "g",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": 100,
    "weight_unit": "g",
    "shelf_life_days": 360
  },
  {
    "sku": "MASSEP_D",
    "name": "Massepain Duo",
    "package_size": 100,
    "package_unit": "g",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": 100,
    "weight_unit": "g",
    "shelf_life_days": 240
  },
  {
    "sku": "GUIMAU_D",
    "name": "Cubes Guimauve Duo",
    "package_size": 100,
    "package_unit": "g",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": 100,
    "weight_unit": "g",
    "shelf_life_days": 240
  },
  {
    "sku": "GRANOL_N",
    "name": "Granola fruit rouges Chocolat Noir",
    "package_size": 100,
    "package_unit": "g",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": 100,
    "weight_unit": "g",
    "shelf_life_days": 360
  },
  {
    "sku": "GRANOL_L",
    "name": "Granola fruit rouges Chocolat Lait",
    "package_size": 100,
    "package_unit": "g",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": 100,
    "weight_unit": "g",
    "shelf_life_days": 360
  },
  {
    "sku": "MUESLI_N",
    "name": "Tuiles muesli Chocolat Noir",
    "package_size": 100,
    "package_unit": "g",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": 100,
    "weight_unit": "g",
    "shelf_life_days": 360
  },
  {
    "sku": "MUESLI_L",
    "name": "Tuiles muesli Chocolat Lait",
    "package_size": 100,
    "package_unit": "g",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": 100,
    "weight_unit": "g",
    "shelf_life_days": 360
  },
  {
    "sku": "SABLE_N",
    "name": "Sablés Chocolat noir",
    "package_size": 100,
    "package_unit": "g",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": 100,
    "weight_unit": "g",
    "shelf_life_days": 240
  },
  {
    "sku": "BALLS_N_N",
    "name": "Noisettes du Piémont IGP – enrobées Choc Noir",
    "package_size": 100,
    "package_unit": "g",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": 100,
    "weight_unit": "g",
    "shelf_life_days": 360
  },
  {
    "sku": "BALLS_N_L",
    "name": "Noisettes du Piémont IGP – enrobées Choc Lait",
    "package_size": 100,
    "package_unit": "g",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": 100,
    "weight_unit": "g",
    "shelf_life_days": 360
  },
  {
    "sku": "BALLS_A_N",
    "name": "Amandes grillées & salées - enrobées Choc Noir",
    "package_size": 100,
    "package_unit": "g",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": 100,
    "weight_unit": "g",
    "shelf_life_days": 360
  },
  {
    "sku": "BALLS_A_L",
    "name": "Amandes grillées & salées - enrobées Choc Lait",
    "package_size": 100,
    "package_unit": "g",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": 100,
    "weight_unit": "g",
    "shelf_life_days": 360
  },
  {
    "sku": "OURSON_N",
    "name": "Nounours Guimauve Chocolat Noir",
    "package_size": 100,
    "package_unit": "g",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": 100,
    "weight_unit": "g",
    "shelf_life_days": 240
  },
  {
    "sku": "OURSON_L",
    "name": "Nounours Guimauve Chocolat Lait",
    "package_size": 100,
    "package_unit": "g",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": 100,
    "weight_unit": "g",
    "shelf_life_days": 240
  },
  {
    "sku": "STNIC_N",
    "name": "St Nicolas 150 mm Chocolat Noir",
    "package_size": 1,
    "package_unit": "pcs",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": null,
    "weight_unit": "g",
    "shelf_life_days": 360
  },
  {
    "sku": "STNIC_L",
    "name": "St Nicolas 150 mm Chocolat Lait",
    "package_size": 1,
    "package_unit": "pcs",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": null,
    "weight_unit": "g",
    "shelf_life_days": 360
  },
  {
    "sku": "STNIC_B",
    "name": "St Nicolas 150 mm Chocolat Blanc",
    "package_size": 1,
    "package_unit": "pcs",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": null,
    "weight_unit": "g",
    "shelf_life_days": 240
  },
  {
    "sku": "SAPIN_N",
    "name": "Sapin de Noël Plein Chocolat Noir",
    "package_size": 1,
    "package_unit": "pcs",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": null,
    "weight_unit": "g",
    "shelf_life_days": 240
  },
  {
    "sku": "SAPIN_L",
    "name": "Sapin de Noël Plein Chocolat Lait",
    "package_size": 1,
    "package_unit": "pcs",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": null,
    "weight_unit": "g",
    "shelf_life_days": 360
  },
  {
    "sku": "SAPIN_B",
    "name": "Sapin de Noël Plein Chocolat Blanc",
    "package_size": 1,
    "package_unit": "pcs",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": null,
    "weight_unit": "g",
    "shelf_life_days": 360
  },
  {
    "sku": "SAPIN_F_N",
    "name": "Sapin de Noël Fourré Praliné Noisettes du Piémont IGP - Choc Noir",
    "package_size": 1,
    "package_unit": "pcs",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": null,
    "weight_unit": "g",
    "shelf_life_days": 360
  },
  {
    "sku": "SAPIN_F_L",
    "name": "Sapin de Noël Fourré Praliné Noisettes du Piémont IGP - Choc Lait",
    "package_size": 1,
    "package_unit": "pcs",
    "vat_rate": "0.06",
    "is_active": 1,
    "weight_grams": null,
    "weight_unit": "g",
    "shelf_life_days": 360
  }
];

async function call(label, path, payload) {
  console.log('--- ' + label);
  let res, text;
  try {
    res = await fetch('/supplier' + path, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(payload),
    });
    text = await res.text();
  } catch (e) {
    console.log('  fetch failed: ' + e.message);
    return null;
  }
  let parsed = null;
  try { parsed = JSON.parse(text); } catch (e) {}
  console.log('  POST /supplier' + path);
  console.log('  HTTP ' + res.status + ' ' + res.statusText);
  console.log('  raw body: ' + (text ? text.slice(0, 3000) : '(empty)'));
  if (parsed) console.log('  parsed:', parsed);
  const ok = res.status < 400 && !(parsed && parsed.success === false);
  console.log('  => ' + (ok ? 'OK' : 'REJECTED'));
  return { ok, status: res.status, text, parsed };
}

(async () => {
  console.log('=== CDT import diagnostics: ' + PRODUCTS.length + ' produktow ===');

  const a = await call('1. tablica (dokladnie tak, jak wysyla modal)',
                       '/ajax/catalog/products/import', PRODUCTS);
  if (a && a.ok) { console.log('\nImport przeszedl - odswiez katalog.'); return; }

  const b = await call('2. koperta {"products": [...]} (jak Twoj plik eksportu)',
                       '/ajax/catalog/products/import', { products: PRODUCTS });
  if (b && b.ok) { console.log('\nDziala koperta {"products": [...]} - poprawie plik.'); return; }

  const c = await call('3. tablica z jednym produktem',
                       '/ajax/catalog/products/import', [PRODUCTS[0]]);
  if (c && c.ok) { console.log('\nDziala pojedynczy produkt - problem dotyczy konkretnej pozycji.'); return; }

  if (TRY_SINGLE_CREATE) {
    await call('4. zwykly POST jednego produktu (TWORZY REKORD)',
               '/ajax/catalog/products', PRODUCTS[0]);
  } else {
    console.log('\n4. pominiete. TRY_SINGLE_CREATE = true sprobuje utworzyc jeden');
    console.log('   produkt zwyklym POST-em - to oddziela "zle pola" od "zla koperta".');
  }

  console.log('\nSkopiuj cale powyzsze wyjscie i wklej mi je.');
})();
