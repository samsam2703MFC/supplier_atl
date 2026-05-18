<?php
namespace App\Supplier\app\Repositories\Me;

use App\Supplier\core\Http\ApiClient;

class AnalyticsRepository
{
    public function __construct(
        private ApiClient $apiClient
    ) {}

    public function getSummary(int $supplierId): array
    {
        $response = $this->apiClient->get("/material-suppliers/{$supplierId}/analytics/summary");
        return $response['data'] ?? [];
    }

    public function getRevenue(int $supplierId, array $params = []): array
    {
        $url = "/material-suppliers/{$supplierId}/analytics/revenue";
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        $response = $this->apiClient->get($url);
        return $response['data'] ?? [];
    }

    public function getTopProducts(int $supplierId, array $params = []): array
    {
        $url = "/material-suppliers/{$supplierId}/analytics/top-products";
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        $response = $this->apiClient->get($url);
        return $response['data'] ?? [];
    }

    public function getRevenueByShop(int $supplierId, array $params = []): array
    {
        $url = "/material-suppliers/{$supplierId}/analytics/revenue-by-shop";
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        $response = $this->apiClient->get($url);
        return $response['data'] ?? [];
    }

    public function getShops(int $supplierId): array
    {
        $response = $this->apiClient->get("/material-suppliers/{$supplierId}/analytics/shops");
        return $response['data'] ?? [];
    }

    public function getRawMaterialSales(int $supplierId, array $params = []): array
    {
        $url = "/material-suppliers/{$supplierId}/analytics/raw-material-sales";
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        $response = $this->apiClient->get($url);
        return $response['data'] ?? [];
    }

    public function getRawMaterialSalesAllShops(int $supplierId, array $params = []): array
    {
        $url = "/material-suppliers/{$supplierId}/analytics/raw-material-sales-all-shops";
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        $response = $this->apiClient->get($url);
        return $response['data'] ?? [];
    }
}
