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

    public function getById(int $supplierId, int $orderId): array
    {
        $response = $this->apiClient->get("/material-suppliers/{$supplierId}/orders/{$orderId}?include=products");
        return $response['data'] ?? [];
    }

    public function getDetails(int $supplierId, int $orderId): array
    {
        return $this->apiClient->get("/material-suppliers/{$supplierId}/orders/{$orderId}/read-model");
    }

    public function accept(int $supplierId, int $orderId, array $payload): array
    {
        return $this->apiClient->post("/material-suppliers/{$supplierId}/orders/{$orderId}/accept", $payload);
    }

    public function reject(int $supplierId, int $orderId, array $payload): array
    {
        return $this->apiClient->post("/material-suppliers/{$supplierId}/orders/{$orderId}/reject", $payload);
    }

    public function cancel(int $supplierId, int $orderId, array $payload): array
    {
        return $this->apiClient->post("/material-suppliers/{$supplierId}/orders/{$orderId}/cancel", $payload);
    }

    public function updateFinalItems(int $supplierId, int $orderId, array $payload): array
    {
        return $this->apiClient->put("/material-suppliers/{$supplierId}/orders/{$orderId}/final-items", $payload);
    }

    public function updateTransport(int $supplierId, int $orderId, array $payload): array
    {
        return $this->apiClient->put("/material-suppliers/{$supplierId}/orders/{$orderId}/transport", $payload);
    }

    public function finalizationCheck(int $supplierId, int $orderId, array $payload): array
    {
        return $this->apiClient->post("/material-suppliers/{$supplierId}/orders/{$orderId}/finalization-check", $payload);
    }

    public function finalize(int $supplierId, int $orderId, array $payload): array
    {
        return $this->apiClient->post("/material-suppliers/{$supplierId}/orders/{$orderId}/finalize", $payload);
    }

    public function getCarriers(int $supplierId): array
    {
        return $this->apiClient->get("/material-suppliers/{$supplierId}/carriers");
    }

    public function createCarrier(int $supplierId, array $payload): array
    {
        return $this->apiClient->post("/material-suppliers/{$supplierId}/carriers", $payload);
    }

    public function updateCarrier(int $supplierId, int $carrierId, array $payload): array
    {
        return $this->apiClient->patch("/material-suppliers/{$supplierId}/carriers/{$carrierId}", $payload);
    }

    public function deleteCarrier(int $supplierId, int $carrierId): array
    {
        return $this->apiClient->delete("/material-suppliers/{$supplierId}/carriers/{$carrierId}");
    }

    public function getDocuments(int $supplierId, int $orderId): array
    {
        return $this->apiClient->get("/material-suppliers/{$supplierId}/orders/{$orderId}/documents");
    }

    public function getCmrStatus(int $supplierId, int $orderId): array
    {
        return $this->apiClient->get("/material-suppliers/{$supplierId}/orders/{$orderId}/documents/cmr/status");
    }

    public function generateDifferencesDocument(int $supplierId, int $orderId): array
    {
        return $this->apiClient->post("/material-suppliers/{$supplierId}/orders/{$orderId}/documents/difference", []);
    }

    public function generateReleaseDocument(int $supplierId, int $orderId): array
    {
        return $this->apiClient->post("/material-suppliers/{$supplierId}/orders/{$orderId}/documents/release", []);
    }

    public function generateCmrDocument(int $supplierId, int $orderId): array
    {
        return $this->apiClient->post("/material-suppliers/{$supplierId}/orders/{$orderId}/documents/cmr", []);
    }

    public function getOrderDocument(int $supplierId, int $orderId, int $documentId): array
    {
        return $this->apiClient->get("/material-suppliers/{$supplierId}/orders/{$orderId}/documents/{$documentId}", false);
    }

    public function getDocument(int $supplierId, int $orderId): array
    {
        return $this->apiClient->get("/material-suppliers/{$supplierId}/orders/{$orderId}/document", false);
    }

    public function getBulkDocuments(int $supplierId, array $orderIds): array
    {
        return $this->apiClient->postToGetMultiplePDF(
            "/material-suppliers/{$supplierId}/orders/documents",
            ['order_ids' => array_values($orderIds)]
        );
    }
}
