import json, os

keys_to_copy = [
    'sku', 'unit', 'category', 'no_category', 'vat_rate', 'waste_perc', 'description',
    'section_basic', 'section_pricing', 'section_extra',
    'manage_categories', 'category_name', 'category_delete_confirm',
    'edit_category', 'delete_category', 'category_name_exists',
    'initial_price_optional', 'package_price', 'package_qty',
    'price_per_unit', 'package_price_hint',
]

base = '/home/szymon/projects/tfb/app/supplier/src/core/I18n/translations/page'
for lang in ['pl', 'en', 'fr', 'it', 'nl']:
    rm_path  = os.path.join(base, lang, 'raw_materials.json')
    rec_path = os.path.join(base, lang, 'recipes.json')
    with open(rm_path, 'r', encoding='utf-8') as f:
        rm = json.load(f)
    with open(rec_path, 'r', encoding='utf-8') as f:
        rec = json.load(f)
    added = []
    for k in keys_to_copy:
        if k not in rec and k in rm:
            rec[k] = rm[k]
            added.append(k)
    with open(rec_path, 'w', encoding='utf-8') as f:
        json.dump(rec, f, ensure_ascii=False, indent=2)
    print(f'{lang}: added {len(added)} keys: {added}')
