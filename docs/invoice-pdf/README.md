# Facture fournisseur — PDF aux couleurs L'Atelier By

`faktura_supplier.php` est le fragment de service (backend) qui génère le PDF de
facture fournisseur, réécrit avec la typo et la charte du portail
(`public/assets/css/design-system.css`) et la mise en page du bon de commande
(`src/app/Views/orders/print.twig`) :

- **Typo** : Gotham Light (corps), Gotham Medium (libellés, totaux), Vank (titre).
- **Couleurs** : Ruby Red `#8D1D2C`, Abricot `#F2C9A0`, beige `#F4EFE8`, noir `#222222`, gris `#666666`.
- **Mise en page** : logo + « Portail Fournisseur », titre / numéro / pastille statut,
  cartes émetteur et client, ligne d'informations (dates, paiement, IBAN), tableau à
  lignes alternées, totaux HT / TVA / TTC, pied de page sur chaque page.

Aperçu : [`example/facture-exemple.pdf`](example/facture-exemple.pdf) (données fictives).

## Installation côté backend

1. Remplacer, dans le service facture, les méthodes `getPdfForSupplier`, `renderPdf`,
   `renderPdfParty` et `renderPdfLabelValue` par le contenu de `faktura_supplier.php`
   (constantes, propriété `$pdfFonts` et helpers `pdf*` compris). `assertSupplierOrder`,
   `mapItemsForPdf` et les repositories existants sont réutilisés tels quels ;
   `fillTableToInvoice` n'est plus appelé.
2. Copier le dossier `brand/` dans `<backend>/resources/brand/pdf/`, ou définir la
   constante `INVOICE_PDF_BRAND_DIR` vers l'emplacement choisi.
3. Rien d'autre : les définitions TCPDF des polices (`*.php`, `*.z`, `*.ctg.z`) sont
   déjà générées. Si elles manquent, elles sont régénérées au premier rendu à partir
   des `.ttf` (dossier inscriptible requis). Sans ressources, le PDF sort en Helvetica.

## Points d'attention

- `mapItemsForPdf()` doit renvoyer une ligne par article, dans l'ordre des 8 colonnes
  (`#`, produit, qté, remise, net, TVA, total net, total brut), valeurs déjà formatées.
- Le bloc totaux lit `total_net` / `total_tax` / `total_gross` sur la facture (alias
  `total_netto`, `total_vat`, `total_brutto`, `value_*`, `amount_*`) ; s'il n'y a rien, il
  est omis. La TVA est déduite de brut − net si elle n'est pas stockée.
- Nouvelles clés de traduction (domaine `invoice`, repli anglais intégré) :
  `supplier_portal`, `status_issued`, `generated_on`, `page`.
- Vank est limité à l'ASCII dans le design system (`unicode-range`) : un titre
  accentué bascule sur Gotham, comme dans le navigateur.

## Polices

TCPDF n'importe pas les OpenType à contours CFF (`OTTO`), format des Gotham du
portail. `tools/otf2ttf.py` (fontTools) les convertit en TrueType :

```bash
pip install fonttools
python3 tools/otf2ttf.py Gotham_Light.otf=gothamlight.ttf Gotham_Medium.otf=gothammedium.ttf
```

`GC_Vank.ttf` est copié tel quel en `vank.ttf`. Les noms de fichiers déterminent les
familles TCPDF (`gothamlight`, `gothammedium`, `vank`) utilisées par le code.
