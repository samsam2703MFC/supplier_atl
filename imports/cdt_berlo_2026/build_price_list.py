#!/usr/bin/env python3
"""
Buduje plik cennika do importu, mapujac SKU dostawcy na product_id z katalogu.

Import cennika w panelu operuje na product_id (wewnetrzne ID katalogu), a nie na
SKU - dlatego produkty musza byc najpierw zaimportowane do katalogu.

Krok 1: zaimportuj products_import.json (Katalog > "Importuj produkty")
Krok 2: pobierz katalog - w przegladarce, zalogowany w panelu:
            fetch('/ajax/catalog/products').then(r => r.json()).then(j =>
              console.log(JSON.stringify(j)))
        i zapisz wynik jako catalog.json
Krok 3:
    # cennik dla wszystkich sklepow (data obowiazywania wybierana w modalu):
    python3 build_price_list.py --catalog catalog.json --scope all-shops

    # cennik dla jednego klienta (data obowiazywania w pliku):
    python3 build_price_list.py --catalog catalog.json --scope client \
        --valid-from 2026-10-01
"""

import argparse
import json
import sys
from pathlib import Path


def load_catalog(path):
    """Akceptuje {'data': [...]}, goła liste produktow albo mape {sku: id}."""
    payload = json.loads(Path(path).read_text(encoding="utf-8"))

    if isinstance(payload, dict) and "data" in payload:
        payload = payload["data"]

    if isinstance(payload, dict):
        return {str(sku): int(pid) for sku, pid in payload.items()}

    if isinstance(payload, list):
        mapping = {}
        for item in payload:
            sku, pid = item.get("sku"), item.get("id")
            if sku and pid is not None:
                mapping[str(sku)] = int(pid)
        return mapping

    raise SystemExit(f"Nieznany format pliku katalogu: {path}")


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--catalog", required=True,
                    help="JSON z /ajax/catalog/products (albo mapa {sku: id})")
    ap.add_argument("--prices", default="prices_by_sku.json")
    ap.add_argument("--scope", choices=["all-shops", "client"], default="all-shops")
    ap.add_argument("--valid-from", help="RRRR-MM-DD; wymagane dla --scope client")
    ap.add_argument("--section", choices=["year_round", "holiday"],
                    help="opcjonalnie: tylko jedna sekcja cennika")
    ap.add_argument("--out", help="plik wyjsciowy (domyslnie wg scope)")
    args = ap.parse_args()

    if args.scope == "client" and not args.valid_from:
        raise SystemExit("--scope client wymaga --valid-from RRRR-MM-DD")

    catalog = load_catalog(args.catalog)
    rows = json.loads(Path(args.prices).read_text(encoding="utf-8"))
    if args.section:
        rows = [r for r in rows if r["section"] == args.section]

    prices, missing = [], []
    for row in rows:
        pid = catalog.get(row["sku"])
        if pid is None:
            missing.append(row["sku"])
            continue
        entry = {"product_id": pid, "price_net": row["price_net"]}
        if args.scope == "client":
            entry["valid_from"] = args.valid_from
        prices.append(entry)

    if missing:
        print("UWAGA: brak w katalogu (pominieto): " + ", ".join(missing),
              file=sys.stderr)

    out = Path(args.out or f"price_list_import.{args.scope}.json")
    out.write_text(
        json.dumps({"prices": prices}, ensure_ascii=False, indent=2) + "\n",
        encoding="utf-8",
    )
    print(f"{out}: {len(prices)} pozycji cennikowych"
          + (f", {len(missing)} bez dopasowania" if missing else ""))


if __name__ == "__main__":
    main()
