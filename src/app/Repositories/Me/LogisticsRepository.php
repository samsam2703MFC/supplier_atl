<?php
namespace App\Supplier\app\Repositories\Me;

use App\Supplier\core\Http\ApiClient;

class LogisticsRepository
{
    public function __construct(
        private ApiClient $apiClient
    ) {}

    public function getUpcomingDeliveries(int $supplierId): array
    {
        $response = $this->apiClient->get("/material-suppliers/{$supplierId}/upcoming-deliveries");
        return $response['data'] ?? [];
    }
}
