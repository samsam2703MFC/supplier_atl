# ANALIZA I TESTY - Katalog Produktów
**Data analizy:** 2026-02-23
**Analizowany moduł:** Zarządzanie produktami w katalogu dostawcy
---
## 1. STRUKTURA DANYCH
### Model Produktu (ProductModel.php)
✅ **ZMODYFIKOWANY** - dodano nowe pola:
- `active` (bool) - czy produkt jest aktywny
- `weight_grams` (int) - waga w gramach  
- `weight_unit` (string) - jednostka wagi (domyślnie 'g')
- `shelf_life_days` (int) - termin przydatności w dniach
- `storage_info` (string) - warunki przechowywania (jednojęzyczne)
- `preparation_info` (array) - sposób przygotowania (wielojęzyczny: pl/en/fr/nl)
- `composition` (array) - skład produktu (wielojęzyczny: pl/en/fr/nl)
### Struktura JSON z eksportu
```json
{
  "sku": 1003221,
  "name": "Abricot & Cerise - 22⌀",
  "category_id": 620,
  "active": true,
  "weight_grams": 1200,
  "weight_unit": "g",
  "package_size": 8,
  "shelf_life_days": 120,
  "storage_info": "Température...",
  "preparation_info": "laten ontdooien...",
  "composition": "morela w puszcze...",
  "allergens": [...]
}
```
---
## 2. WIDOK (catalog.twig)
### Modal produktu - CO DZIAŁA ✅
1. **Podstawowe pola (STARY MODEL)**:
   - Kategoria (select)
   - SKU (text)
   - Nazwa (text)
   - Package Size (number)
   - Package Unit (select: kg/g/ml/l/pcs)
   - VAT Rate (number)
2. **Mechanizmy**:
   - Walidacja pól wymaganych
   - Edycja istniejących produktów
   - Dodawanie nowych produktów
   - Komunikaty błędów
### Modal produktu - CO NIE DZIAŁA ❌
**BRAK NOWYCH PÓL W FORMULARZU:**
1. ❌ `active` (checkbox)
2. ❌ `weight_grams` (input number)
3. ❌ `weight_unit` (select lub input)
4. ❌ `shelf_life_days` (input number)
5. ❌ `storage_info` (textarea)
6. ❌ `preparation_info` (textarea wielojęzyczna)
7. ❌ `composition` (textarea wielojęzyczna)
**BRAK ZAKŁADEK JĘZYKOWYCH** dla pól wielojęzycznych (pl/en/fr/nl)
---
## 3. JAVASCRIPT - CO DZIAŁA ✅
### Funkcje działające:
1. ✅ `loadProducts()` - ładowanie listy produktów
2. ✅ `renderTable()` - wyświetlanie tabeli
3. ✅ `openCreateModal()` - otwieranie modalu dodawania
4. ✅ `openEditModal(product)` - otwieranie modalu edycji
5. ✅ `saveProduct()` - zapis produktu (POST/PUT)
6. ✅ `confirmDelete(product)` - usuwanie produktu
7. ✅ Filtrowanie po kategorii
8. ✅ Wyszukiwanie po nazwie/SKU
9. ✅ Zarządzanie alergenami (osobny modal)
10. ✅ Zarządzanie składnikami (osobny modal)
### Funkcje NIE obsługujące nowych pól ❌
**openCreateModal()** - linia 726-745:
```javascript
// ❌ BRAK inicjalizacji dla:
// - productActive
// - productWeightGrams
// - productWeightUnit  
// - productShelfLifeDays
// - productStorageInfo
// - productPreparationInfo (langs)
// - productComposition (langs)
```
**openEditModal()** - linia 747-766:
```javascript
// ❌ BRAK wypełniania dla nowych pól
```
**saveProduct()** - linia 771-847:
```javascript
const payload = {
    category_id: ...,
    sku: ...,
    name: ...,
    package_size: ...,
    package_unit: ...,
    vat_rate: ...
    // ❌ BRAK nowych pól w payload
};
```
---
## 4. TŁUMACZENIA (catalog.json)
✅ **ZAKTUALIZOWANE** - dodano klucze:
- `active`, `inactive`
- `weight`, `shelf_life`, `shelf_life_days`
- `storage_info`, `preparation_info`, `composition`
- `lang_pl`, `lang_en`, `lang_fr`, `lang_nl`
- `language_tabs_hint`
- `basic_data_tab`, `multilang_tab`
- Placeholdery i hinty dla nowych pól
---
## 5. TESTY FUNKCJONALNOŚCI
### Test 1: Dodawanie produktu (STARY MODEL)
**Status:** ✅ DZIAŁA
- Modal otwiera się poprawnie
- Pola są walidowane
- Produkt zapisuje się przez API
- Lista odświeża się po zapisie
### Test 2: Edycja produktu (STARY MODEL)  
**Status:** ✅ DZIAŁA
- Dane produktu ładują się do formularza
- Zmiany zapisują się przez API (PUT)
- Lista odświeża się po edycji
### Test 3: Usuwanie produktu
**Status:** ✅ DZIAŁA
- Pojawia się potwierdzenie
- Produkt usuwa się przez API (DELETE)
- Lista odświeża się
### Test 4: Zarządzanie alergenami
**Status:** ✅ DZIAŁA
- Modal z checkboxami
- AJAX assign/unassign
- Badge'e w tabeli aktualizują się
### Test 5: Dodawanie/edycja z NOWYMI POLAMI
**Status:** ❌ NIE DZIAŁA
- Brak pól w formularzu
- Brak wysyłania nowych danych do API
- Backend może nie otrzymywać nowych wartości
---
## 6. PROBLEMY DO NAPRAWY
### KRYTYCZNE 🔴
1. **Modal nie zawiera nowych pól** - użytkownik nie może wprowadzić:
   - weight_grams, shelf_life_days, storage_info
   - preparation_info (wielojęzycznie)
   - composition (wielojęzycznie)
2. **JavaScript nie zbiera nowych danych** - funkcja `saveProduct()` nie wysyła nowych pól do API
3. **Brak zakładek językowych** - dla pól wielojęzycznych (pl/en/fr/nl)
### ŚREDNIE 🟡  
4. **Tabela nie pokazuje nowych pól** - użytkownik nie widzi:
   - Status active/inactive
   - Waga produktu
   - Termin przydatności
5. **Brak walidacji** dla nowych pól (np. czy weight_grams > 0)
### NISKIE 🟢
6. Brak filtra po statusie active/inactive
7. Brak sortowania po nowych polach
---
## 7. PLAN NAPRAWY
### Priorytet 1 - MODAL (nowe pola)
1. Dodać zakładki: "Podstawowe" / "Języki"
2. Zakładka "Podstawowe":
   - Checkbox `active`
   - Input `weight_grams` + `weight_unit`
   - Input `shelf_life_days`
   - Textarea `storage_info`
3. Zakładka "Języki":
   - Tabs: PL / EN / FR / NL
   - Textarea `preparation_info[lang]`
   - Textarea `composition[lang]`
### Priorytet 2 - JAVASCRIPT
1. `openCreateModal()` - inicjalizacja nowych pól
2. `openEditModal()` - ładowanie nowych pól z produktu
3. `saveProduct()` - zbieranie i wysyłanie nowych pól w payload
### Priorytet 3 - TABELA
1. Dodać kolumnę "Status" (active badge)
2. Pokazać wagę w badge'u (np. "1200g")
3. Pokazać termin przydatności
### Priorytet 4 - WALIDACJA
1. Sprawdzanie czy weight_grams > 0
2. Sprawdzanie czy shelf_life_days > 0
3. Sprawdzanie czy storage_info nie jest puste
---
## 8. KOMPATYBILNOŚĆ Z API
### Endpoint: POST /ajax/catalog/products
**Obecnie wysyłane:**
```json
{
  "category_id": 1,
  "sku": "1002128",
  "name": "Produkt",
  "package_size": 8,
  "package_unit": "pcs",
  "vat_rate": "0.23"
}
```
**Powinno być wysyłane (NOWY MODEL):**
```json
{
  "category_id": 1,
  "sku": "1002128", 
  "name": "Produkt",
  "package_size": 8,
  "package_unit": "pcs",
  "vat_rate": "0.23",
  "active": true,
  "weight_grams": 1200,
  "weight_unit": "g",
  "shelf_life_days": 120,
  "storage_info": "Temp. -18°C",
  "preparation_info": {
    "pl": "Rozmrozić przed podaniem",
    "en": "Defrost before serving",
    "fr": "Décongeler avant de servir",
    "nl": "Ontdooien voor serveren"
  },
  "composition": {
    "pl": "mąka, cukier, masło...",
    "en": "flour, sugar, butter...",
    "fr": "farine, sucre, beurre...",
    "nl": "meel, suiker, boter..."
  }
}
```
⚠️ **UWAGA:** Backend musi obsługiwać nowe pola!
---
## 9. PODSUMOWANIE
### CO DZIAŁA ✅
- Model ProductModel.php zaktualizowany
- Tłumaczenia catalog.json zaktualizowane
- Stary mechanizm CRUD działa (bez nowych pól)
- Alergeny działają
- Składniki działają (jeśli backend obsługuje)
### CO NIE DZIAŁA ❌
- **Modal nie ma nowych pól** - użytkownik nie może ich wprowadzić
- **JavaScript nie wysyła nowych danych** - API nie otrzymuje nowych wartości
- **Tabela nie pokazuje nowych informacji** - brak wizualizacji
- **Brak zakładek językowych** - dla pól wielojęzycznych
### WNIOSEK
**Aplikacja wymaga modyfikacji widoku catalog.twig:**
1. Rozbudowa modalu o nowe pola (z zakładkami językowymi)
2. Aktualizacja JavaScript (openCreateModal, openEditModal, saveProduct)
3. Rozbudowa tabeli o nowe kolumny/informacje
4. Dodanie walidacji dla nowych pól
**Następny krok:** Implementacja zmian w catalog.twig
