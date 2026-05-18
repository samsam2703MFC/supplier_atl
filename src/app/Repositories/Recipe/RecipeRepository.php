<?php
namespace App\Supplier\app\Repositories\Recipe;

use App\Supplier\core\Http\ApiClient;

/**
 * Klient HTTP dla zasobu recipes w API (scoped do supplierId).
 */
class RecipeRepository
{
    public function __construct(
        private ApiClient $apiClient
    ) {}

    public function getAll(int $supplierId, array $filters = []): array
    {
        $query = [];
        foreach (['name', 'product_id', 'is_archived'] as $key) {
            if (isset($filters[$key]) && $filters[$key] !== '' && $filters[$key] !== null) {
                $query[$key] = $filters[$key];
            }
        }

        $qs = !empty($query) ? '?' . http_build_query($query) : '';
        $response = $this->apiClient->get("/material-suppliers/{$supplierId}/recipes{$qs}");

        return $response['data'] ?? [];
    }

    public function getById(int $supplierId, int $id): array
    {
        $response = $this->apiClient->get("/material-suppliers/{$supplierId}/recipes/{$id}");
        return $response['data'] ?? [];
    }

    public function insert(int $supplierId, array $data): array
    {
        return $this->apiClient->post("/material-suppliers/{$supplierId}/recipes", $data);
    }

    public function update(int $supplierId, int $id, array $data): array
    {
        return $this->apiClient->patch("/material-suppliers/{$supplierId}/recipes/{$id}", $data);
    }

    public function delete(int $supplierId, int $id): array
    {
        return $this->apiClient->delete("/material-suppliers/{$supplierId}/recipes/{$id}");
    }

    public function archive(int $supplierId, int $id): array
    {
        return $this->apiClient->patch("/material-suppliers/{$supplierId}/recipes/{$id}/archive", []);
    }

    public function restore(int $supplierId, int $id): array
    {
        return $this->apiClient->patch("/material-suppliers/{$supplierId}/recipes/{$id}/restore", []);
    }

    public function recalculateOverhead(int $supplierId, int $id): array
    {
        return $this->apiClient->post("/material-suppliers/{$supplierId}/recipes/{$id}/overhead/recalculate", []);
    }
}
