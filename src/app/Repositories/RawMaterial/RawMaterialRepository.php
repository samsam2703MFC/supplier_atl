<?php
namespace App\Supplier\app\Repositories\RawMaterial;

use App\Supplier\core\Http\ApiClient;

/**
 * Klient HTTP dla zasobu raw_materials w API (scoped do supplierId).
 *
 * Stan iteracji:
 *   - iter. 2: list / insert / update.
 *   - iter. 4: alergeny (list / assign / unassign).
 *   - iter. 5: historia cen (list / add — append-only).
 */
class RawMaterialRepository
{
    public function __construct(
        private ApiClient $apiClient
    ) {}

    public function getAll(int $supplierId, array $filters = []): array
    {
        $query = ['include' => 'allergens'];
        foreach (['name', 'sku', 'id_category', 'is_archived'] as $key) {
            if (isset($filters[$key]) && $filters[$key] !== '' && $filters[$key] !== null) {
                $query[$key] = $filters[$key];
            }
        }

        $qs = '?' . http_build_query($query);
        $response = $this->apiClient->get("/material-suppliers/{$supplierId}/raw-materials{$qs}");

        return $response['data'] ?? [];
    }

    public function insert(int $supplierId, array $data): array
    {
        return $this->apiClient->post("/material-suppliers/{$supplierId}/raw-materials", $data);
    }

    public function update(int $supplierId, int $id, array $data): array
    {
        return $this->apiClient->patch("/material-suppliers/{$supplierId}/raw-materials/{$id}", $data);
    }

    // -------------------------------------------------------------------
    // Allergens (iter. 4)
    // -------------------------------------------------------------------

    public function getAllergens(int $supplierId, int $id): array
    {
        $response = $this->apiClient->get("/material-suppliers/{$supplierId}/raw-materials/{$id}/allergens");
        return $response['data'] ?? [];
    }

    public function assignAllergen(int $supplierId, int $id, array $data): array
    {
        return $this->apiClient->post("/material-suppliers/{$supplierId}/raw-materials/{$id}/allergens", $data);
    }

    public function unassignAllergen(int $supplierId, int $id, int $allergenId): array
    {
        return $this->apiClient->delete("/material-suppliers/{$supplierId}/raw-materials/{$id}/allergens/{$allergenId}");
    }

    // -------------------------------------------------------------------
    // Price history (iter. 5, append-only)
    // -------------------------------------------------------------------

    public function getPriceHistory(int $supplierId, int $id): array
    {
        $response = $this->apiClient->get("/material-suppliers/{$supplierId}/raw-materials/{$id}/price-history");
        return $response['data'] ?? [];
    }

    public function addPriceEntry(int $supplierId, int $id, array $data): array
    {
        return $this->apiClient->post("/material-suppliers/{$supplierId}/raw-materials/{$id}/price-history", $data);
    }

    // -------------------------------------------------------------------
    // Archive / restore / delete (iter. 6)
    // -------------------------------------------------------------------

    public function archive(int $supplierId, int $id): array
    {
        return $this->apiClient->patch("/material-suppliers/{$supplierId}/raw-materials/{$id}/archive", []);
    }

    public function restore(int $supplierId, int $id): array
    {
        return $this->apiClient->patch("/material-suppliers/{$supplierId}/raw-materials/{$id}/restore", []);
    }

    public function delete(int $supplierId, int $id): array
    {
        return $this->apiClient->delete("/material-suppliers/{$supplierId}/raw-materials/{$id}");
    }
}

