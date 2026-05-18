import json
import os

base = '/home/szymon/projects/tfb/app/supplier/src/core/I18n/translations/page'

new_keys = {
    'pl': {
        'quick_add_raw_material': 'Nowy surowiec',
        'raw_material_name_label': 'Nazwa surowca',
        'raw_material_unit_label': 'Jednostka miary',
        'raw_material_sku_label': 'SKU (opcjonalnie)',
        'add_raw_material': 'Dodaj surowiec',
        'raw_material_added': 'Surowiec został dodany do bazy.',
        'labor_defaults_btn': 'Domyślne',
        'labor_defaults_modal_title': 'Domyślne parametry pracy',
        'labor_defaults_hint': 'Wartości zapisane tutaj będą automatycznie wstępnie uzupełniane w każdej nowo tworzonej recepturze.',
        'labor_defaults_save': 'Zapisz jako domyślne',
        'labor_defaults_saved': 'Domyślne parametry pracy zostały zapisane.',
        'labor_defaults_prefilled': 'Wartości uzupełnione z Twoich domyślnych ustawień pracy. Możesz je zmienić przed zapisaniem.',
    },
    'en': {
        'quick_add_raw_material': 'New raw material',
        'raw_material_name_label': 'Raw material name',
        'raw_material_unit_label': 'Unit of measure',
        'raw_material_sku_label': 'SKU (optional)',
        'add_raw_material': 'Add raw material',
        'raw_material_added': 'Raw material added to the database.',
        'labor_defaults_btn': 'Defaults',
        'labor_defaults_modal_title': 'Default labor parameters',
        'labor_defaults_hint': 'Values saved here will be automatically pre-filled in every newly created recipe.',
        'labor_defaults_save': 'Save as default',
        'labor_defaults_saved': 'Default labor parameters saved.',
        'labor_defaults_prefilled': 'Values pre-filled from your default labor settings. You can change them before saving.',
    },
    'fr': {
        'quick_add_raw_material': 'Nouvelle matière première',
        'raw_material_name_label': 'Nom de la matière première',
        'raw_material_unit_label': "Unité de mesure",
        'raw_material_sku_label': 'SKU (optionnel)',
        'add_raw_material': 'Ajouter la matière première',
        'raw_material_added': 'Matière première ajoutée à la base.',
        'labor_defaults_btn': 'Défauts',
        'labor_defaults_modal_title': "Paramètres de main-d'œuvre par défaut",
        'labor_defaults_hint': "Les valeurs enregistrées ici seront automatiquement pré-remplies dans chaque nouvelle recette créée.",
        'labor_defaults_save': 'Enregistrer par défaut',
        'labor_defaults_saved': "Paramètres de main-d'œuvre par défaut enregistrés.",
        'labor_defaults_prefilled': "Valeurs pré-remplies depuis vos paramètres de main-d'œuvre par défaut. Vous pouvez les modifier avant d'enregistrer.",
    },
    'it': {
        'quick_add_raw_material': 'Nuova materia prima',
        'raw_material_name_label': 'Nome materia prima',
        'raw_material_unit_label': 'Unità di misura',
        'raw_material_sku_label': 'SKU (opzionale)',
        'add_raw_material': 'Aggiungi materia prima',
        'raw_material_added': 'Materia prima aggiunta al database.',
        'labor_defaults_btn': 'Predefiniti',
        'labor_defaults_modal_title': 'Parametri manodopera predefiniti',
        'labor_defaults_hint': 'I valori salvati qui verranno automaticamente precompilati in ogni nuova ricetta creata.',
        'labor_defaults_save': 'Salva come predefinito',
        'labor_defaults_saved': 'Parametri manodopera predefiniti salvati.',
        'labor_defaults_prefilled': 'Valori precompilati dalle impostazioni manodopera predefinite. Puoi modificarli prima di salvare.',
    },
    'nl': {
        'quick_add_raw_material': 'Nieuwe grondstof',
        'raw_material_name_label': 'Naam grondstof',
        'raw_material_unit_label': 'Meeteenheid',
        'raw_material_sku_label': 'SKU (optioneel)',
        'add_raw_material': 'Grondstof toevoegen',
        'raw_material_added': 'Grondstof toegevoegd aan de database.',
        'labor_defaults_btn': 'Standaard',
        'labor_defaults_modal_title': 'Standaard arbeidsparameters',
        'labor_defaults_hint': 'Waarden die hier worden opgeslagen, worden automatisch ingevuld in elk nieuw aangemaakt recept.',
        'labor_defaults_save': 'Opslaan als standaard',
        'labor_defaults_saved': 'Standaard arbeidsparameters opgeslagen.',
        'labor_defaults_prefilled': 'Waarden ingevuld vanuit uw standaard arbeidsparameters. U kunt ze wijzigen voor het opslaan.',
    },
}

for lang, keys in new_keys.items():
    path = os.path.join(base, lang, 'recipes.json')
    with open(path, 'r', encoding='utf-8') as f:
        data = json.load(f)
    for k, v in keys.items():
        data[k] = v
    with open(path, 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=2)
    print(f'Updated {lang}: {len(keys)} keys added')

print('Done.')
