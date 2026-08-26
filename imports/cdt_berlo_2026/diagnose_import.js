// CDT import - diagnostyka. Pokazuje PRAWDZIWY blad API zamiast komunikatu
// "L'importation a echoue" z modala.
//
// Zalogowany w panelu, dowolna strona panelu -> konsola -> wklej calosc.
// Nic nie tworzy dopoki nie ustawisz TRY_SINGLE_CREATE = true.
//
// Krok 1 wysyla te same 25 produktow co plik i wypisuje status + surowa
// odpowiedz. Krok 2 (opcjonalny) probuje utworzyc JEDEN produkt przez
// dzialajacy formularz "Dodaj produkt" - jesli krok 1 zawiedzie, a krok 2
// przejdzie, problem jest w kopercie importu, nie w polach.

const TRY_SINGLE_CREATE = false;   // true = utworzy 1 prawdziwy produkt (SPOON-N)

const PRODUCTS = [
    {
      "sku": "SPOON-N",
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
      "sku": "SPOON-E",
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
      "sku": "MATINE-N",
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
      "sku": "MATINE-L",
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
      "sku": "MASSEP-D",
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
      "sku": "GUIMAU-D",
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
      "sku": "GRANOL-N",
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
      "sku": "GRANOL-L",
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
      "sku": "MUESLI-N",
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
      "sku": "MUESLI-L",
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
      "sku": "SABLE-N",
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
      "sku": "BALLS-N-N",
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
      "sku": "BALLS-N-L",
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
      "sku": "BALLS-A-N",
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
      "sku": "BALLS-A-L",
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
      "sku": "OURSON-N",
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
      "sku": "OURSON-L",
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
      "sku": "STNIC-N",
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
      "sku": "STNIC-L",
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
      "sku": "STNIC-B",
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
      "sku": "SAPIN-N",
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
      "sku": "SAPIN-L",
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
      "sku": "SAPIN-B",
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
      "sku": "SAPIN-F-N",
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
      "sku": "SAPIN-F-L",
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

async function call(path, payload) {
  const res = await fetch('/supplier' + path, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body: JSON.stringify(payload),
  });
  const text = await res.text();
  let parsed = null;
  try { parsed = JSON.parse(text); } catch (e) {}
  console.log('POST /supplier' + path);
  console.log('  HTTP ' + res.status + ' ' + res.statusText);
  console.log('  raw body: ' + text.slice(0, 3000));
  if (parsed) console.log('  parsed:', parsed);
  return { status: res.status, text, parsed };
}

(async () => {
  console.log('=== 1. import 25 produktow (tablica, tak jak modal) ===');
  const a = await call('/ajax/catalog/products/import', PRODUCTS);

  if (a.status < 400 && a.parsed && (a.parsed.success === true)) {
    console.log('OK - import przeszedl.');
    return;
  }

  console.log('');
  console.log('=== 2. ta sama tablica opakowana w {"products": [...]} ===');
  await call('/ajax/catalog/products/import', { products: PRODUCTS });

  if (TRY_SINGLE_CREATE) {
    console.log('');
    console.log('=== 3. pojedynczy produkt przez zwykly POST (TWORZY DANE) ===');
    await call('/ajax/catalog/products', PRODUCTS[0]);
  } else {
    console.log('');
    console.log('Krok 3 pominiety. Ustaw TRY_SINGLE_CREATE = true, zeby sprobowac');
    console.log('utworzyc jeden produkt zwyklym POST-em (utworzy prawdziwy rekord).');
  }

  console.log('');
  console.log('Skopiuj cale powyzsze wyjscie - pokazuje, ktore pole odrzuca API.');
})();
