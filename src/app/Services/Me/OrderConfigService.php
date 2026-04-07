<?php
namespace App\Supplier\app\Services\Me;

use App\Supplier\app\Repositories\Me\SupplierOrderConfigRepository;
use App\Supplier\core\Support\GlobalRegistry;

class OrderConfigService
{
    public function __construct(
        private SupplierOrderConfigRepository $orderConfigRepository
    ) {}

    public function getConfig(): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->orderConfigRepository->getOrderConfig($supplierId);
    }

    public function saveConfig(array $data): bool
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];

        $payload = [
            'min_order_value' => (float) ($data['min_order_value'] ?? 0),
            'currency_code'   => strtoupper(trim($data['currency_code'] ?? 'EUR')),
            'tiers'           => [],
        ];

        // Przetwarzamy maksymalnie 5 tierów przesłanych z formularza
        for ($i = 1; $i <= 5; $i++) {
            $minKey    = "tier_{$i}_min";
            $maxKey    = "tier_{$i}_max";
            $feeKey    = "tier_{$i}_fee";
            $activeKey = "tier_{$i}_active";

            // Tier aktywny tylko jeśli pole fee jest wypełnione
            if (!isset($data[$feeKey]) || $data[$feeKey] === '') {
                continue;
            }

            $maxValue = (isset($data[$maxKey]) && $data[$maxKey] !== '')
                ? (float) $data[$maxKey]
                : null;

            $payload['tiers'][] = [
                'tier_order'      => $i,
                'min_order_value' => (float) ($data[$minKey] ?? 0),
                'max_order_value' => $maxValue,
                'fee_amount'      => (float) $data[$feeKey],
            ];
        }

        return $this->orderConfigRepository->saveOrderConfig($supplierId, $payload);
    }
}

