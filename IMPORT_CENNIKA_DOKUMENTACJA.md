# Mechanizm Importu Cennika dla Klienta

## Przegląd

Zaimplementowany został pełny mechanizm importu cennika z pliku JSON dla wybranego klienta. Mechanizm składa się z:

1. **Interfejsu użytkownika** - przycisk "Importuj cennik" i modal z wyborem pliku
2. **Walidacji po stronie klienta** - sprawdzanie formatu i rozmiaru pliku
3. **API Endpoint** - `POST /ajax/clients/{clientId}/price-list/import`
4. **Backend** - przetwarzanie danych JSON przez API

## Jak używać

### 1. Otwórz cennik klienta
- Przejdź do listy klientów: `/clients`
- Kliknij "Cennik" przy wybranym kliencie

### 2. Kliknij "Importuj cennik"
- Znajduje się obok przycisku "Dodaj pozycję"
- Otworzy się modal z opcjami importu

### 3. Wybierz plik JSON
- Kliknij "Wybierz plik JSON"
- Wybierz plik z danymi cennika
- Plik musi być w formacie `.json`
- Maksymalny rozmiar: 10 MB

### 4. Rozpocznij import
- Po wybraniu pliku aktywuje się przycisk "Rozpocznij import"
- Kliknij go, aby wysłać dane do API
- Poczekaj na potwierdzenie

### 5. Sprawdź wyniki
- Po pomyślnym imporcie zobaczysz komunikat sukcesu
- Strona automatycznie się przeładuje, pokazując zaktualizowane dane

## Format pliku JSON

```json
{
  "prices": [
    {
      "product_id": 1,
      "price_net": 12.50,
      "valid_from": "2026-03-01"
    },
    {
      "product_id": 2,
      "price_net": 25.00,
      "valid_from": "2026-03-01"
    }
  ]
}
```

### Pola obowiązkowe:
- `product_id` (integer) - ID produktu z katalogu
- `price_net` (decimal) - cena netto
- `valid_from` (date) - data obowiązywania w formacie YYYY-MM-DD

## Implementacja techniczna

### Frontend (Twig + JavaScript)
- **Widok**: `src/app/Views/client/price_list.twig`
- **Modal importu**: HTML z formularzem wyboru pliku
- **Walidacja**: JavaScript sprawdzający format i rozmiar pliku
- **Upload**: FileReader API do parsowania JSON, wysyłka przez AJAX

### Backend (PHP)
1. **Controller**: `src/app/Http/Controllers/Price/PriceController.php`
   - Metoda: `ajaxImportPriceList($clientId)`
   - Route: `POST /ajax/clients/{clientId}/price-list/import`

2. **Service**: `src/app/Services/Price/PriceListService.php`
   - Metoda: `importPriceList($clientId, $data)`
   - Obsługa logiki biznesowej

3. **Repository**: `src/app/Repositories/Price/PriceListRepository.php`
   - Metoda: `importPriceList($supplierId, $clientId, $data)`
   - Komunikacja z API: `POST /material-suppliers/{supplierId}/shops/{clientId}/price-lists/import`

4. **Routing**: `src/core/Bootstrap/Routes/ClientRoutes.php`
   - Rejestracja trasy POST

### API Endpoint
```
POST /material-suppliers/{supplierId}/shops/{clientId}/price-lists/import

Body (JSON):
{
  "prices": [
    {
      "product_id": 1,
      "price_net": 12.50,
      "valid_from": "2026-03-01"
    },
    ...
  ]
}

Response (success):
{
  "success": true,
  "data": {
    "imported": 10
  }
}

Response (error):
{
  "success": false,
  "message": "Komunikat błędu"
}
```

## Tłumaczenia

Dodane klucze w `src/core/I18n/translations/page/pl/client.json`:
- `import_price_list`: "Importuj cennik"
- `import_price_list_subtitle`: "Wgraj plik JSON z cenami dla klienta"
- `import_price_list_modal_hint`: "Wybierz plik JSON zawierający dane cennika..."
- `import_price_list_warning`: "Operacja importu może zająć trochę czasu..."
- `import_price_list_success`: "Import cennika zakończony pomyślnie"
- `import_price_list_failed`: "Import cennika nie powiódł się..."
- `price_items`: "pozycji cennikowych"
- I inne klucze wspólne (close, cancel, save, itp.)

## Walidacja

### Po stronie klienta (JavaScript):
- ✅ Format pliku: tylko `.json`
- ✅ Rozmiar pliku: max 10 MB
- ✅ Poprawność JSON: parsowanie przed wysłaniem

### Po stronie serwera (API):
- ✅ Struktura danych
- ✅ Typy pól
- ✅ Istnienie produktów
- ✅ Daty w przyszłości (jeśli wymagane)

## Obsługa błędów

1. **Nieprawidłowy format pliku**
   - Komunikat: "Nieprawidłowy format pliku. Wybierz plik JSON."

2. **Plik za duży**
   - Komunikat: "Plik jest za duży. Maksymalny rozmiar to 10MB."

3. **Błąd parsowania JSON**
   - Komunikat: "Nieprawidłowy plik JSON. Sprawdź zawartość pliku."

4. **Błąd API**
   - Komunikat: Treść błędu z API lub "Import cennika nie powiódł się. Sprawdź format pliku."

5. **Brak połączenia**
   - Komunikat: "Błąd odczytu pliku. Spróbuj ponownie."

## Przykładowy plik

Plik przykładowy znajduje się w: `price_list_import_example.json`

## Testowanie

1. Przygotuj plik JSON z danymi cennika
2. Zaloguj się do systemu jako dostawca
3. Przejdź do cennika wybranego klienta
4. Kliknij "Importuj cennik"
5. Wybierz przygotowany plik
6. Rozpocznij import
7. Sprawdź wyniki

## Bezpieczeństwo

- ✅ Walidacja typu i rozmiaru pliku
- ✅ Parsowanie JSON z obsługą błędów
- ✅ Autoryzacja użytkownika (supplier_id z sesji)
- ✅ Walidacja clientId w URL
- ✅ Escape danych w widokach

## Przyszłe usprawnienia

- [ ] Podgląd importowanych danych przed zatwierdzeniem
- [ ] Raport z importu (udane/nieudane pozycje)
- [ ] Opcja nadpisywania istniejących cen
- [ ] Import CSV jako alternatywa dla JSON
- [ ] Eksport aktualnego cennika do JSON/CSV
- [ ] Walidacja duplikatów przed importem

