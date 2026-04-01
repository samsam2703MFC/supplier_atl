# ✅ CHECKLIST - Naprawa Katalogu Produktów
## 🔴 KRYTYCZNE (musi być zrobione)
### Modal produktu - HTML
- [ ] Dodać strukturę zakładek (nav-tabs: "Podstawowe" / "Języki")
- [ ] **Zakładka "Podstawowe":**
  - [ ] Checkbox `productActive` (Aktywny)
  - [ ] Input number `productWeightGrams` (Waga w gramach)
  - [ ] Select `productWeightUnit` (Jednostka: g/kg)
  - [ ] Input number `productShelfLifeDays` (Termin przydatności dni)
  - [ ] Textarea `productStorageInfo` (Warunki przechowywania)
- [ ] **Zakładka "Języki" - 4 sub-tabs (PL/EN/FR/NL):**
  - [ ] Textarea `productPreparationInfo_pl/en/fr/nl` (Sposób przygotowania)
  - [ ] Textarea `productComposition_pl/en/fr/nl` (Skład)
### JavaScript - openCreateModal()
- [ ] Inicjalizacja `productActive` (checked = true)
- [ ] Inicjalizacja `productWeightGrams` ("")
- [ ] Inicjalizacja `productWeightUnit` ("g")
- [ ] Inicjalizacja `productShelfLifeDays` ("")
- [ ] Inicjalizacja `productStorageInfo` ("")
- [ ] Inicjalizacja 4x `productPreparationInfo_[lang]` ("")
- [ ] Inicjalizacja 4x `productComposition_[lang]` ("")
### JavaScript - openEditModal(product)
- [ ] Wypełnienie `productActive` (product.active)
- [ ] Wypełnienie `productWeightGrams` (product.weight_grams)
- [ ] Wypełnienie `productWeightUnit` (product.weight_unit)
- [ ] Wypełnienie `productShelfLifeDays` (product.shelf_life_days)
- [ ] Wypełnienie `productStorageInfo` (product.storage_info)
- [ ] Wypełnienie 4x `productPreparationInfo_[lang]` (product.preparation_info[lang])
- [ ] Wypełnienie 4x `productComposition_[lang]` (product.composition[lang])
### JavaScript - saveProduct()
- [ ] Dodać do payload: `active` (checkbox.checked)
- [ ] Dodać do payload: `weight_grams` (Number)
- [ ] Dodać do payload: `weight_unit` (string)
- [ ] Dodać do payload: `shelf_life_days` (Number)
- [ ] Dodać do payload: `storage_info` (string)
- [ ] Dodać do payload: `preparation_info` (obiekt {pl, en, fr, nl})
- [ ] Dodać do payload: `composition` (obiekt {pl, en, fr, nl})
---
## 🟡 ŚREDNIE (ważne dla UX)
### Tabela - renderTable()
- [ ] Dodać kolumnę/badge "Status" (Aktywny/Nieaktywny)
- [ ] Dodać badge z wagą (np. "1200g")
- [ ] Opcjonalnie: pokazać termin przydatności
### Walidacja - saveProduct()
- [ ] Sprawdzić czy `weight_grams > 0` (jeśli wypełnione)
- [ ] Sprawdzić czy `shelf_life_days > 0` (jeśli wypełnione)
- [ ] Opcjonalnie: wymagać `storage_info` (jeśli potrzebne)
---
## 🟢 OPCJONALNE (nice-to-have)
### Filtrowanie
- [ ] Dodać filtr po statusie (Aktywne/Nieaktywne/Wszystkie)
### Sortowanie
- [ ] Dodać sortowanie po wadze
- [ ] Dodać sortowanie po terminie przydatności
### UX
- [ ] Tooltip z pełnym tekstem composition przy hover
- [ ] Podgląd preparation_info w tooltipie
---
## 🧪 TESTY PO NAPRAWIE
### Test 1: Dodawanie nowego produktu
1. [ ] Otworzyć modal "Dodaj produkt"
2. [ ] Wypełnić wszystkie pola (podstawowe + języki)
3. [ ] Zapisać
4. [ ] Sprawdzić w konsoli czy payload zawiera nowe pola
5. [ ] Sprawdzić czy produkt pojawił się w tabeli
### Test 2: Edycja istniejącego produktu
1. [ ] Otworzyć modal edycji
2. [ ] Sprawdzić czy pola są wypełnione wartościami z produktu
3. [ ] Zmienić wartości (np. wagę, skład)
4. [ ] Zapisać
5. [ ] Sprawdzić czy zmiany się zapisały
### Test 3: Wyświetlanie w tabeli
1. [ ] Sprawdzić czy tabela pokazuje status (badge Aktywny/Nieaktywny)
2. [ ] Sprawdzić czy tabela pokazuje wagę
3. [ ] Sprawdzić czy dane są aktualne po edycji
### Test 4: Walidacja
1. [ ] Spróbować zapisać wagę = 0 (powinien pokazać błąd)
2. [ ] Spróbować zapisać shelf_life = 0 (powinien pokazać błąd)
3. [ ] Sprawdzić czy komunikaty błędów są czytelne
---
## 📝 NOTATKI TECHNICZNE
### Struktura pól wielojęzycznych w JS:
```javascript
const langs = ['pl', 'en', 'fr', 'nl'];
const preparation_info = {};
const composition = {};
langs.forEach(lang => {
    preparation_info[lang] = el(`productPreparationInfo_${lang}`).value.trim();
    composition[lang] = el(`productComposition_${lang}`).value.trim();
});
```
### Przykład payload do API:
```json
{
  "category_id": 1,
  "sku": "1002128",
  "name": "Test",
  "package_size": 8,
  "package_unit": "pcs",
  "vat_rate": "0.05",
  "active": true,
  "weight_grams": 1200,
  "weight_unit": "g",
  "shelf_life_days": 120,
  "storage_info": "Temp. -18°C",
  "preparation_info": {
    "pl": "Rozmrozić",
    "en": "Defrost",
    "fr": "Décongeler",
    "nl": "Ontdooien"
  },
  "composition": {
    "pl": "mąka, cukier, masło",
    "en": "flour, sugar, butter",
    "fr": "farine, sucre, beurre",
    "nl": "meel, suiker, boter"
  }
}
```
---
## ⚠️ PRZED ROZPOCZĘCIEM
1. [ ] **Backup pliku catalog.twig** (na wszelki wypadek)
2. [ ] **Sprawdzić czy backend obsługuje nowe pola** (konsultacja z backend dev)
3. [ ] **Przygotować dane testowe** (przykładowe produkty do dodania/edycji)
---
## 🎯 PRIORYTET DZIAŁAŃ
**Dzień 1:**
1. ✅ Analiza wykonana
2. Modal HTML - dodać pola (zakładki + inputy)
3. JavaScript - openCreateModal + openEditModal
4. Test ręczny: czy pola się wyświetlają
**Dzień 2:**
1. JavaScript - saveProduct (rozszerzyć payload)
2. Walidacja podstawowa
3. Test ręczny: czy dane się wysyłają
**Dzień 3:**
1. Tabela - dodać nowe kolumny/badge'e
2. Testy finalne
3. Poprawki bugów
---
## 📞 KONTAKT W RAZIE PROBLEMÓW
- Backend nie przyjmuje nowych pól → konsultacja z backend team
- Layout się psuje → sprawdzić Bootstrap classes
- Zakładki nie działają → sprawdzić czy Bootstrap JS załadowany
- API zwraca błąd → sprawdzić payload w console.log przed wysłaniem
---
**Status:** ⏳ Oczekuje na implementację
**Ostatnia aktualizacja:** 2026-02-23
