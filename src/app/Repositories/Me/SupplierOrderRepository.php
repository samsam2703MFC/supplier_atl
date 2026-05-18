<?php
namespace App\Supplier\app\Repositories\Me;

use App\Supplier\core\Http\ApiClient;

class SupplierOrderRepository
{
    public function __construct(
        private ApiClient $apiClient
    ) {}

    public function getAll(int $supplierId, array $params = []): array
    {
        $url = "/material-suppliers/{$supplierId}/orders";
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        $response = $this->apiClient->get($url);
        return $response['data'] ?? [];
    }
}
