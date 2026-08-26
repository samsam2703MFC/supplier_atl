// CDT - import produktow POJEDYNCZO.
//
// Uzyj tego, jesli w panelu dziala tylko plik z jednym produktem: API
// odrzuca tablice bledem INVALID_JSON_STRUCTURE, ale przyjmuje pojedynczy
// obiekt (tak samo robi modal, gdy plik ma dokladnie jedna pozycje).
//
// Zalogowany jako CDT -> dowolna strona panelu -> F12 -> Console -> wklej.
// TWORZY PRAWDZIWE PRODUKTY. Odswiez katalog po zakonczeniu.

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

(async () => {
  const okList = [], failList = [];

  for (const p of PRODUCTS) {
    let res, text;
    try {
      res = await fetch('/supplier/ajax/catalog/products/import', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ ...p, active: 1 }),
      });
      text = await res.text();
    } catch (e) {
      failList.push(p.sku + ' - fetch failed: ' + e.message);
      continue;
    }
    let parsed = null;
    try { parsed = JSON.parse(text); } catch (e) {}
    const bad = res.status >= 400 || (parsed && parsed.success === false);
    if (bad) {
      const why = (parsed && (parsed.description || parsed.message)) || text.slice(0, 200);
      failList.push(p.sku + ' - HTTP ' + res.status + ' - ' + why);
      console.warn('FAIL ' + p.sku + ': ' + why);
    } else {
      okList.push(p.sku);
      console.log('ok   ' + p.sku + (parsed && parsed.inserted_id ? ' -> id ' + parsed.inserted_id : ''));
    }
  }

  console.log('');
  console.log('=== zaimportowano ' + okList.length + ' z ' + PRODUCTS.length + ' ===');
  if (failList.length) {
    console.log('nieudane (' + failList.length + '):');
    failList.forEach(f => console.log('  ' + f));
    console.log('Popraw przyczyne i uruchom ponownie - powtorzone SKU moga sie zdublowac.');
  } else {
    console.log('Wszystkie przeszly. Odswiez Katalog, potem zbuduj cennik.');
  }
})();
