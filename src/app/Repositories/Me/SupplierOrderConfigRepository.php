<?php
namespace App\Supplier\app\Repositories\Me;

use App\Supplier\core\Http\ApiClient;

class SupplierOrderConfigRepository
{
    public function __construct(
        private ApiClient $apiClient
    ) {}

    public function getOrderConfig(int $supplierId): array
    {
        $response = $this->apiClient->get("/material-suppliers/{$supplierId}/order-config");
        return $response['data'] ?? [
            'min_order_value' => 0.00,
            'currency_code'   => 'EUR',
            'tiers'           => [],
        ];
    }

    public function saveOrderConfig(int $supplierId, array $data): bool
    {
        $response = $this->apiClient->put("/material-suppliers/{$supplierId}/order-config", $data);
        return isset($response['status']) && $response['status'] !== 'error';
    }
}

