    /* =========================================================================
     *  Facture fournisseur — PDF aux couleurs L'Atelier By
     *
     *  Typo et couleurs reprennent public/assets/css/design-system.css du portail
     *  (Ruby Red / Abricot / Beige, Gotham + Vank) et la mise en page du bon de
     *  commande (orders/print.twig) : logo + filet rubis, cartes émetteur/client
     *  sur fond beige, libellés capitales espacées, tableau à lignes alternées,
     *  totaux soulignés de rubis, pied de page discret sur chaque page.
     *
     *  Ressources attendues dans INVOICE_PDF_BRAND_DIR
     *  (par défaut <projet>/resources/brand/pdf) :
     *    atelierby-dark.png                          logo (PNG sans transparence)
     *    gothamlight.ttf, gothammedium.ttf, vank.ttf polices de la marque, TrueType
     *    <police>.php / .z / .ctg.z                   fichiers TCPDF générés une fois
     *                                                 par TCPDF_FONTS::addTTFfont()
     *                                                 (livrés, ou générés au premier
     *                                                 rendu si le dossier est inscriptible)
     *  Sans ces ressources le document se génère quand même, en Helvetica.
     * ======================================================================= */

    // Palette — design-system.css
    private const PDF_COLOR_INK = [34, 34, 34];            // --color-text                 #222222
    private const PDF_COLOR_MUTED = [102, 102, 102];       // --color-text-muted           #666666
    private const PDF_COLOR_PRIMARY = [141, 29, 44];       // --color-primary  Ruby Red    #8D1D2C
    private const PDF_COLOR_ABRICOT = [242, 201, 160];     // --color-secondary Abricot    #F2C9A0
    private const PDF_COLOR_ON_ABRICOT = [107, 68, 32];    // --color-on-abricot           #6b4420
    private const PDF_COLOR_SURFACE_2 = [244, 239, 232];   // --color-background-secondary #F4EFE8
    private const PDF_COLOR_STRIPE = [248, 246, 243];      // rgba(234,228,220,.35) sur blanc (lignes paires)
    private const PDF_COLOR_BORDER = [215, 215, 215];      // --color-border-secondary rgba(34,34,34,.18)
    private const PDF_COLOR_BORDER_SOFT = [233, 233, 233]; // --color-border-tertiary  rgba(34,34,34,.10)

    // Typo — --font-ui (Gotham) pour le texte, --font-display (Vank) pour le titre
    private const PDF_FONT_BODY = 'gothamlight';      // Gotham Light 300  : corps de texte
    private const PDF_FONT_EMPHASIS = 'gothammedium'; // Gotham Medium 500 : libellés, mises en avant, totaux
    private const PDF_FONT_DISPLAY = 'vank';          // Vank              : titre du document
    private const PDF_FONT_FALLBACK = 'helvetica';

    private const PDF_LOGO_FILE = 'atelierby-dark.png';
    private const PDF_MARGIN = 15;             // mm
    private const PDF_MARGIN_BOTTOM = 28;      // mm — réserve le pied de page
    private const PDF_LINE_HEIGHT_RATIO = 1.45;
    private const PDF_TRACKING_ADMIN = 0.2;    // mm ≈ --tracking-admin (0.08em à 7 pt)
    private const PDF_TRACKING_NAV = 0.15;     // mm ≈ --tracking-nav   (0.06em à 7 pt)

    /** @var array<string, string> familles TCPDF résolues par rôle (body / emphasis / display) */
    private array $pdfFonts = [];

    public function getPdfForSupplier(int $supplierId, int $orderId, int $invoiceId): array
    {
        $this->assertSupplierOrder($supplierId, $orderId);
        $invoice = $this->invoiceRepository->findByOrderAndId($orderId, $invoiceId);
        if (empty($invoice) || (int) ($invoice['id_supplier'] ?? 0) !== $supplierId) {
            throw new CustomException('SUPPLIER_INVOICE_NOT_FOUND', 'supplier_invoice', 404);
        }
        if (($invoice['status'] ?? null) !== 'ISSUED') {
            throw new CustomException('SUPPLIER_INVOICE_NOT_ACTIVE', 'supplier_invoice', 409);
        }

        $pdf = new TCPDFModel('p', 'mm', 'a4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(self::PDF_MARGIN, self::PDF_MARGIN, self::PDF_MARGIN);
        $pdf->SetAutoPageBreak(true, self::PDF_MARGIN_BOTTOM);
        $pdf->setCellPaddings(0, 0, 0, 0);
        $pdf->setCellHeightRatio(self::PDF_LINE_HEIGHT_RATIO);
        $this->registerPdfBrandFonts($pdf);
        $pdf->AddPage();
        $this->renderPdf($pdf, $invoice, $this->invoiceItemRepository->findByInvoice((int) $invoice['id']));

        return [
            'pdf' => $pdf->Output('', 'S'),
            'filename' => 'invoice-' . $invoice['invoice_number'] . '.pdf',
        ];
    }

    private function renderPdf(TCPDFModel $pdf, array $invoice, array $items): void
    {
        $language = Language::getCallLanguageCode();
        $translate = static fn (string $key, string $fallback): string => ($value = getTranslation('invoice', $key, $language)) === $key ? $fallback : $value;
        $date = static fn ($value): string => $value ? date('d.m.Y', strtotime((string) $value)) : '—';

        $this->renderPdfHeader($pdf, $invoice, $translate);
        $this->renderPdfParties($pdf, $invoice, $translate);
        $this->renderPdfMeta($pdf, [
            [$translate('invoice_date', 'Invoice date'), $date($invoice['issued_at'] ?? null)],
            [$translate('due_date', 'Due date'), $date($invoice['due_date'] ?? null)],
            [$translate('payment_type', 'Payment type'), $translate('bank_transfer', 'Bank transfer')],
            ['IBAN', (string) ($invoice['issuer_iban'] ?? '')],
        ]);
        $this->renderPdfItems($pdf, [
            '#',
            $translate('product', 'Product'),
            $translate('quantity_short', 'Qty'),
            $translate('reduction_short', 'Disc.'),
            $translate('netto_short', 'Net'),
            $translate('tax', 'VAT'),
            $translate('total_netto_short', 'Total net'),
            $translate('total_gross_short', 'Total gross'),
        ], $this->mapItemsForPdf($items));
        $this->renderPdfTotals($pdf, $invoice, $translate);
        $this->renderPdfFooters($pdf, $invoice, $translate);
    }

    /** Logo + « Portail fournisseur » à gauche, titre Vank / numéro rubis / pastille statut à droite, filet rubis. */
    private function renderPdfHeader(TCPDFModel $pdf, array $invoice, callable $translate): void
    {
        $x = self::PDF_MARGIN;
        $y = self::PDF_MARGIN;
        $width = $this->pdfContentWidth($pdf);

        $logoWidth = 44.5; // 168 px du bon de commande imprimé
        $logo = $this->pdfBrandDir() . '/' . self::PDF_LOGO_FILE;
        if (is_file($logo)) {
            $pdf->Image($logo, $x, $y, $logoWidth, 0, 'PNG');
            $leftBottom = $pdf->getImageRBY();
        } else {
            $this->pdfText($pdf, 'display', 18, self::PDF_COLOR_INK);
            $pdf->SetXY($x, $y);
            $pdf->Cell($logoWidth, 7.5, "L'ATELIER", 0, 0, 'L');
            $leftBottom = $y + 7.5;
        }
        $this->pdfText($pdf, 'emphasis', 7, self::PDF_COLOR_MUTED, self::PDF_TRACKING_ADMIN);
        $pdf->SetXY($x, $leftBottom + 1.9);
        $pdf->Cell($width / 2, 3.6, mb_strtoupper($translate('supplier_portal', 'Supplier portal'), 'UTF-8'), 0, 0, 'L');
        $leftBottom += 1.9 + 3.6;

        $title = $this->pdfUcfirst($translate('invoice', 'Invoice'));
        $this->pdfText($pdf, $this->pdfDisplayRole($title), 18, self::PDF_COLOR_INK);
        $pdf->SetXY($x, $y);
        $pdf->Cell($width, 7.5, $title, 0, 0, 'R');
        $this->pdfText($pdf, 'body', 8.5, self::PDF_COLOR_PRIMARY, 0.05);
        $pdf->SetXY($x, $y + 8.3);
        $pdf->Cell($width, 4, (string) ($invoice['invoice_number'] ?? ''), 0, 0, 'R');
        $status = strtolower((string) ($invoice['status'] ?? 'issued'));
        $rightBottom = $this->renderPdfPill($pdf, $x + $width, $y + 14.2, $translate('status_' . $status, $this->pdfUcfirst($status)));

        $ruleY = max($leftBottom, $rightBottom) + 3.7;
        $this->pdfLine($pdf, $x, $ruleY, $x + $width, $ruleY, self::PDF_COLOR_PRIMARY, 0.53);
        $pdf->SetY($ruleY + 4.2);
    }

    /** Pastille Abricot (statut), alignée sur son bord droit. Retourne le bas de la pastille. */
    private function renderPdfPill(TCPDFModel $pdf, float $rightX, float $y, string $text): float
    {
        $label = mb_strtoupper($text, 'UTF-8');
        $height = 4.6;
        $this->pdfText($pdf, 'emphasis', 7, self::PDF_COLOR_ON_ABRICOT, self::PDF_TRACKING_NAV);
        $width = $pdf->GetStringWidth($label) + 5.8;
        $pdf->SetFillColor(...self::PDF_COLOR_ABRICOT);
        $pdf->RoundedRect($rightX - $width, $y, $width, $height, $height / 2, '1111', 'F');
        $pdf->SetXY($rightX - $width, $y);
        $pdf->Cell($width, $height, $label, 0, 0, 'C');

        return $y + $height;
    }

    /** Deux cartes beige côte à côte, de même hauteur : émetteur et client. */
    private function renderPdfParties(TCPDFModel $pdf, array $invoice, callable $translate): void
    {
        $x = self::PDF_MARGIN;
        $y = $pdf->GetY();
        $gap = 3.7;
        $padX = 3.4;
        $padY = 2.9;
        $cardWidth = ($this->pdfContentWidth($pdf) - $gap) / 2;
        $innerWidth = $cardWidth - 2 * $padX;
        $buyerX = $x + $cardWidth + $gap;

        // Mesure à blanc pour donner la même hauteur aux deux cartes
        $pdf->startTransaction();
        $issuerBottom = $this->renderPdfParty($pdf, $x + $padX, $y + $padY, $innerWidth, $invoice, 'issuer', $translate);
        $buyerBottom = $this->renderPdfParty($pdf, $buyerX + $padX, $y + $padY, $innerWidth, $invoice, 'buyer', $translate);
        $pdf->rollbackTransaction(true);
        $height = max($issuerBottom, $buyerBottom) - $y + $padY;

        $pdf->SetFillColor(...self::PDF_COLOR_SURFACE_2);
        $pdf->RoundedRect($x, $y, $cardWidth, $height, 2.1, '1111', 'F');
        $pdf->RoundedRect($buyerX, $y, $cardWidth, $height, 2.1, '1111', 'F');
        $this->renderPdfParty($pdf, $x + $padX, $y + $padY, $innerWidth, $invoice, 'issuer', $translate);
        $this->renderPdfParty($pdf, $buyerX + $padX, $y + $padY, $innerWidth, $invoice, 'buyer', $translate);
        $pdf->SetY($y + $height + 3.2);
    }

    private function renderPdfParty(TCPDFModel $pdf, float $x, float $y, float $width, array $invoice, string $prefix, callable $translate): float
    {
        $this->pdfText($pdf, 'emphasis', 7, self::PDF_COLOR_MUTED, self::PDF_TRACKING_ADMIN);
        $pdf->SetXY($x, $y);
        $pdf->Cell($width, 3.6, mb_strtoupper($translate($prefix, ucfirst($prefix)), 'UTF-8'), 0, 2, 'L');

        $this->pdfText($pdf, 'emphasis', 10, self::PDF_COLOR_INK);
        $pdf->MultiCell($width, 5, (string) ($invoice[$prefix . '_name'] ?? ''), 0, 'L', false, 2, $x, $pdf->GetY() + 1.3);

        $this->pdfText($pdf, 'body', 8.5, self::PDF_COLOR_MUTED);
        $address = trim((string) ($invoice[$prefix . '_address_1'] ?? '') . "\n" . (string) ($invoice[$prefix . '_address_2'] ?? ''));
        if ($address !== '') {
            $pdf->MultiCell($width, 4.3, $address, 0, 'L', false, 2, $x, $pdf->GetY() + 0.5);
        }
        if (!empty($invoice[$prefix . '_tax_number'])) {
            $pdf->SetX($x);
            $pdf->Cell($width, 4.3, $translate('tax_num', 'VAT number') . ': ' . $invoice[$prefix . '_tax_number'], 0, 2, 'L');
        }
        if ($prefix === 'issuer' && !empty($invoice['issuer_phone'])) {
            $pdf->SetX($x);
            $pdf->Cell($width, 4.3, $translate('phone', 'Phone') . ': ' . $invoice['issuer_phone'], 0, 2, 'L');
        }

        return $pdf->GetY();
    }

    /**
     * Ligne d'informations clés (libellé en capitales, valeur en Medium), à la manière
     * du bloc dates du bon de commande. Les entrées sans valeur sont ignorées.
     *
     * @param array<int, array{0: string, 1: string}> $entries [libellé, valeur]
     */
    private function renderPdfMeta(TCPDFModel $pdf, array $entries): void
    {
        $left = self::PDF_MARGIN;
        $right = $left + $this->pdfContentWidth($pdf);
        $x = $left;
        $y = $pdf->GetY();
        $gap = 5.8;
        $labelHeight = 3.6;
        $valueHeight = 4.6;

        foreach ($entries as [$label, $value]) {
            if ($value === '' || $value === '—') {
                continue;
            }
            $label = mb_strtoupper($label, 'UTF-8');
            $this->pdfText($pdf, 'emphasis', 7, self::PDF_COLOR_MUTED, self::PDF_TRACKING_ADMIN);
            $labelWidth = $pdf->GetStringWidth($label) + 0.5;
            $this->pdfText($pdf, 'emphasis', 9, self::PDF_COLOR_INK);
            $width = max($labelWidth, $pdf->GetStringWidth($value)) + 1;
            if ($x > $left && $x + $width > $right) {
                $x = $left;
                $y += $labelHeight + $valueHeight + 2.5;
            }
            $pdf->SetXY($x, $y + $labelHeight + 0.4);
            $pdf->Cell($width, $valueHeight, $value, 0, 0, 'L');
            $this->pdfText($pdf, 'emphasis', 7, self::PDF_COLOR_MUTED, self::PDF_TRACKING_ADMIN);
            $pdf->SetXY($x, $y);
            $pdf->Cell($width, $labelHeight, $label, 0, 0, 'L');
            $x += $width + $gap;
        }

        $pdf->SetY($y + $labelHeight + $valueHeight + 4.2);
    }

    /**
     * Tableau des lignes : en-tête en capitales soulignées de rubis, lignes alternées beige,
     * filets fins, coupure de page propre avec reprise de l'en-tête.
     *
     * @param array<int, string>              $headers libellés des 8 colonnes
     * @param array<int, array<int, mixed>>   $rows    lignes de mapItemsForPdf(), dans l'ordre des colonnes
     */
    private function renderPdfItems(TCPDFModel $pdf, array $headers, array $rows): void
    {
        $columns = [[10, 'L'], [49, 'L'], [18, 'R'], [17, 'R'], [19, 'R'], [14, 'R'], [26.5, 'R'], [26.5, 'R']]; // mm, alignement
        $left = self::PDF_MARGIN;
        $width = $this->pdfContentWidth($pdf);
        $scale = $width / array_sum(array_column($columns, 0));
        $padX = 2.1;
        $padY = 1.7;
        $bottomLimit = $pdf->getPageHeight() - self::PDF_MARGIN_BOTTOM;

        $pdf->SetAutoPageBreak(false, self::PDF_MARGIN_BOTTOM);
        $pdf->setCellPaddings($padX, $padY, $padX, $padY);

        $drawHeader = function () use ($pdf, $headers, $columns, $scale, $left, $width): void {
            $this->pdfText($pdf, 'emphasis', 7, self::PDF_COLOR_MUTED, self::PDF_TRACKING_ADMIN);
            $labels = [];
            $height = 0;
            foreach ($columns as $i => [$w, $align]) {
                $labels[$i] = mb_strtoupper((string) ($headers[$i] ?? ''), 'UTF-8');
                $height = max($height, $pdf->getStringHeight($w * $scale, $labels[$i]));
            }
            $y = $pdf->GetY();
            $x = $left;
            foreach ($columns as $i => [$w, $align]) {
                $pdf->MultiCell($w * $scale, $height, $labels[$i], 0, $align, false, 0, $x, $y, true, 0, false, true, $height, 'B');
                $x += $w * $scale;
            }
            $this->pdfLine($pdf, $left, $y + $height, $left + $width, $y + $height, self::PDF_COLOR_PRIMARY, 0.4);
            $pdf->SetY($y + $height);
        };

        $drawHeader();
        foreach (array_values($rows) as $index => $row) {
            $cells = [];
            $height = 0;
            $this->pdfText($pdf, 'body', 8.5, self::PDF_COLOR_INK);
            foreach ($columns as $i => [$w, $align]) {
                $cells[$i] = (string) ($row[$i] ?? '');
                $height = max($height, $pdf->getStringHeight($w * $scale, $cells[$i]));
            }
            if ($pdf->GetY() + $height > $bottomLimit) {
                $pdf->AddPage();
                $drawHeader();
            }
            $y = $pdf->GetY();
            if ($index % 2 === 1) {
                $pdf->SetFillColor(...self::PDF_COLOR_STRIPE);
                $pdf->Rect($left, $y, $width, $height, 'F');
            }
            $x = $left;
            foreach ($columns as $i => [$w, $align]) {
                $this->pdfText($pdf, 'body', 8.5, $i === 0 ? self::PDF_COLOR_MUTED : self::PDF_COLOR_INK);
                $pdf->MultiCell($w * $scale, $height, $cells[$i], 0, $align, false, 0, $x, $y, true, 0, false, true, $height, 'T');
                $x += $w * $scale;
            }
            $this->pdfLine($pdf, $left, $y + $height, $left + $width, $y + $height, self::PDF_COLOR_BORDER_SOFT, 0.13);
            $pdf->SetY($y + $height);
        }

        $pdf->setCellPaddings(0, 0, 0, 0);
        $pdf->SetAutoPageBreak(true, self::PDF_MARGIN_BOTTOM);
    }

    /** Bloc totaux à droite : HT, TVA, puis TTC en Medium rubis sous un filet rubis. */
    private function renderPdfTotals(TCPDFModel $pdf, array $invoice, callable $translate): void
    {
        $totals = $this->pdfInvoiceTotals($invoice);
        if ($totals === []) {
            return;
        }
        $currency = $this->pdfCurrencySymbol($invoice);
        $rows = array_filter([
            [$translate('total_netto_short', 'Total net'), $totals['net']],
            [$translate('tax', 'VAT'), $totals['tax']],
            [$translate('total_gross_short', 'Total gross'), $totals['gross']],
        ], static fn (array $row): bool => $row[1] !== null);

        $width = 70;
        $rowHeight = 6.4;
        $labelWidth = 38;
        $height = count($rows) * $rowHeight + 3;
        if ($pdf->GetY() + $height > $pdf->getPageHeight() - self::PDF_MARGIN_BOTTOM) {
            $pdf->AddPage();
        }
        $x = self::PDF_MARGIN + $this->pdfContentWidth($pdf) - $width;
        $y = $pdf->GetY() + 3;
        $last = array_key_last($rows);
        foreach ($rows as $key => [$label, $amount]) {
            $isGrand = $key === $last;
            if ($isGrand) {
                $this->pdfLine($pdf, $x, $y, $x + $width, $y, self::PDF_COLOR_PRIMARY, 0.4);
                $y += 0.8;
            }
            $this->pdfText($pdf, $isGrand ? 'emphasis' : 'body', $isGrand ? 9.5 : 8.5, $isGrand ? self::PDF_COLOR_INK : self::PDF_COLOR_MUTED);
            $pdf->SetXY($x + 2.1, $y);
            $pdf->Cell($labelWidth, $rowHeight, $label, 0, 0, 'L');
            $this->pdfText($pdf, $isGrand ? 'emphasis' : 'body', $isGrand ? 9.5 : 8.5, $isGrand ? self::PDF_COLOR_PRIMARY : self::PDF_COLOR_INK);
            $pdf->SetXY($x + $labelWidth, $y);
            $pdf->Cell($width - $labelWidth - 2.1, $rowHeight, $this->pdfMoney($amount, $currency), 0, 0, 'R');
            $y += $rowHeight;
        }
        $pdf->SetY($y);
    }

    /** Pied de page sur chaque page : mentions de l'émetteur à gauche, date d'édition, numéro et pagination à droite. */
    private function renderPdfFooters(TCPDFModel $pdf, array $invoice, callable $translate): void
    {
        $pages = $pdf->getNumPages();
        $pdf->SetAutoPageBreak(false, 0);
        for ($page = 1; $page <= $pages; $page++) {
            $pdf->setPage($page);
            $pdf->SetAutoPageBreak(false, 0); // setPage() restaure le saut de page automatique de la page
            $this->renderPdfFooter($pdf, $invoice, $page, $pages, $translate);
        }
        $pdf->lastPage();
        $pdf->SetAutoPageBreak(true, self::PDF_MARGIN_BOTTOM);
    }

    private function renderPdfFooter(TCPDFModel $pdf, array $invoice, int $page, int $pages, callable $translate): void
    {
        $x = self::PDF_MARGIN;
        $width = $this->pdfContentWidth($pdf);
        $y = $pdf->getPageHeight() - self::PDF_MARGIN_BOTTOM + 4;
        $lineHeight = 4;
        $leftWidth = $width * 0.62;
        $this->pdfLine($pdf, $x, $y, $x + $width, $y, self::PDF_COLOR_BORDER, 0.13);
        $y += 3.2;

        $name = (string) ($invoice['issuer_name'] ?? '');
        $address = implode(', ', array_filter([(string) ($invoice['issuer_address_1'] ?? ''), (string) ($invoice['issuer_address_2'] ?? '')], 'strlen'));
        $this->pdfText($pdf, 'emphasis', 7, self::PDF_COLOR_PRIMARY);
        $nameWidth = min($pdf->GetStringWidth($name) + 0.5, $leftWidth);
        $pdf->SetXY($x, $y);
        $pdf->Cell($nameWidth, $lineHeight, $name, 0, 0, 'L');
        if ($address !== '' && $nameWidth < $leftWidth) {
            $this->pdfText($pdf, 'body', 7, self::PDF_COLOR_MUTED);
            $pdf->Cell($leftWidth - $nameWidth, $lineHeight, ($name !== '' ? ' — ' : '') . $address, 0, 0, 'L');
        }
        $details = array_filter([
            !empty($invoice['issuer_tax_number']) ? $translate('tax_num', 'VAT number') . ': ' . $invoice['issuer_tax_number'] : '',
            !empty($invoice['issuer_iban']) ? 'IBAN: ' . $invoice['issuer_iban'] : '',
            !empty($invoice['issuer_phone']) ? $translate('phone', 'Phone') . ': ' . $invoice['issuer_phone'] : '',
        ], 'strlen');
        $this->pdfText($pdf, 'body', 7, self::PDF_COLOR_MUTED);
        $pdf->SetXY($x, $y + $lineHeight);
        $pdf->Cell($leftWidth, $lineHeight, implode(' · ', $details), 0, 0, 'L');

        $pdf->SetXY($x + $leftWidth, $y);
        $pdf->Cell($width - $leftWidth, $lineHeight, $translate('generated_on', 'Generated on') . ' ' . date('d.m.Y H:i'), 0, 0, 'R');
        $pdf->SetXY($x + $leftWidth, $y + $lineHeight);
        $pdf->Cell($width - $leftWidth, $lineHeight, (string) ($invoice['invoice_number'] ?? '') . ' · ' . $translate('page', 'Page') . ' ' . $page . ' / ' . $pages, 0, 0, 'R');
    }

    /* ---------------------------------------------------------------------
     *  Helpers
     * ------------------------------------------------------------------- */

    /**
     * Enregistre les polices de la marque auprès de TCPDF. Les définitions TCPDF
     * (.php/.z/.ctg.z) sont générées à partir des .ttf au premier rendu si elles
     * manquent et que le dossier est inscriptible ; sinon repli sur Helvetica.
     */
    private function registerPdfBrandFonts(TCPDFModel $pdf): void
    {
        $dir = $this->pdfBrandDir();
        $this->pdfFonts = [];
        $families = ['body' => self::PDF_FONT_BODY, 'emphasis' => self::PDF_FONT_EMPHASIS, 'display' => self::PDF_FONT_DISPLAY];
        foreach ($families as $role => $family) {
            $definition = $dir . '/' . $family . '.php';
            $source = $dir . '/' . $family . '.ttf';
            if (!is_file($definition) && is_file($source) && is_writable($dir) && class_exists('TCPDF_FONTS')) {
                TCPDF_FONTS::addTTFfont($source, 'TrueTypeUnicode', '', 32, $dir . '/');
            }
            if (is_file($definition)) {
                $pdf->AddFont($family, '', $definition);
                $this->pdfFonts[$role] = $family;
            }
        }
    }

    private function pdfBrandDir(): string
    {
        return rtrim(defined('INVOICE_PDF_BRAND_DIR') ? INVOICE_PDF_BRAND_DIR : dirname(__DIR__, 3) . '/resources/brand/pdf', '/');
    }

    private function pdfFont(string $role): string
    {
        return $this->pdfFonts[$role] ?? self::PDF_FONT_FALLBACK;
    }

    /** Vank ne couvre que l'ASCII dans le design system (unicode-range) : au-delà, repli sur Gotham. */
    private function pdfDisplayRole(string $text): string
    {
        return preg_match('/^[\x20-\x7E]*$/', $text) ? 'display' : 'body';
    }

    /** @param array{0: int, 1: int, 2: int} $color */
    private function pdfText(TCPDFModel $pdf, string $role, float $size, array $color, float $tracking = 0): void
    {
        $pdf->SetFont($this->pdfFont($role), '', $size);
        $pdf->SetTextColor(...$color);
        $pdf->setFontSpacing($tracking);
    }

    /** @param array{0: int, 1: int, 2: int} $color */
    private function pdfLine(TCPDFModel $pdf, float $x1, float $y1, float $x2, float $y2, array $color, float $lineWidth): void
    {
        $pdf->SetDrawColor(...$color);
        $pdf->SetLineWidth($lineWidth);
        $pdf->Line($x1, $y1, $x2, $y2);
    }

    private function pdfContentWidth(TCPDFModel $pdf): float
    {
        return $pdf->getPageWidth() - 2 * self::PDF_MARGIN;
    }

    private function pdfUcfirst(string $text): string
    {
        return mb_strtoupper(mb_substr($text, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($text, 1, null, 'UTF-8');
    }

    private function pdfMoney($amount, string $currency): string
    {
        return number_format((float) $amount, 2, ',', ' ') . ' ' . $currency;
    }

    private function pdfCurrencySymbol(array $invoice): string
    {
        $code = strtoupper((string) ($invoice['currency'] ?? $invoice['currency_code'] ?? 'EUR'));

        return ['EUR' => '€', 'GBP' => '£', 'USD' => '$', 'PLN' => 'zł'][$code] ?? $code;
    }

    /**
     * Totaux portés par la facture (colonnes usuelles net / taxe / brut) ; [] si la
     * facture n'en porte aucun, le bloc totaux est alors omis.
     *
     * @return array{net: float|null, tax: float|null, gross: float|null}|array{}
     */
    private function pdfInvoiceTotals(array $invoice): array
    {
        $pick = static function (array $keys) use ($invoice): ?float {
            foreach ($keys as $key) {
                if (isset($invoice[$key]) && is_numeric($invoice[$key])) {
                    return (float) $invoice[$key];
                }
            }

            return null;
        };
        $net = $pick(['total_net', 'total_netto', 'net_total', 'value_net', 'amount_net']);
        $tax = $pick(['total_tax', 'total_vat', 'tax_total', 'vat_total', 'value_tax', 'amount_tax']);
        $gross = $pick(['total_gross', 'total_brutto', 'gross_total', 'value_gross', 'amount_gross']);
        if ($net === null && $gross === null) {
            return [];
        }
        if ($tax === null && $net !== null && $gross !== null) {
            $tax = $gross - $net;
        }

        return ['net' => $net, 'tax' => $tax, 'gross' => $gross];
    }
