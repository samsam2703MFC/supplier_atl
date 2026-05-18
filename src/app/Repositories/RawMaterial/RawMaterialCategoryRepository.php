<?php
namespace App\Supplier\app\Repositories\RawMaterial;

use App\Supplier\core\Http\ApiClient;

/**
 * Klient kategorii surowców (scoped per supplier).
 * Iteracja 2: getAll do filtra w UI.
 * Iteracja 7: pełny CRUD kategorii.
 */
class RawMaterialCategoryRepository
{
    public function __construct(
        private ApiClient $apiClient
    ) {}

    public function getAll(int $supplierId): array
    {
        $response = $this->apiClient->get("/material-suppliers/{$supplierId}/raw-material-categories");
        return $response['data'] ?? [];
    }

    public function insert(int $supplierId, array $data): array
    {
        return $this->apiClient->post("/material-suppliers/{$supplierId}/raw-material-categories", $data);
    }

    public function update(int $supplierId, int $id, array $data): array
    {
        return $this->apiClient->patch("/material-suppliers/{$supplierId}/raw-material-categories/{$id}", $data);
    }

    public function delete(int $supplierId, int $id): array
    {
        return $this->apiClient->delete("/material-suppliers/{$supplierId}/raw-material-categories/{$id}");
    }
}

