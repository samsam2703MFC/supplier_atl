<?php
namespace App\Supplier\app\Repositories\Recipe;

use App\Supplier\core\Http\ApiClient;

/**
 * Klient HTTP dla składników receptury w API.
 */
class RecipeIngredientRepository
{
    public function __construct(
        private ApiClient $apiClient
    ) {}

    public function getAll(int $supplierId, int $recipeId): array
    {
        $response = $this->apiClient->get("/material-suppliers/{$supplierId}/recipes/{$recipeId}/ingredients");
        return $response['data'] ?? [];
    }

    public function getSummary(int $supplierId, int $recipeId): array
    {
        $response = $this->apiClient->get("/material-suppliers/{$supplierId}/recipes/{$recipeId}/summary");
        return $response['data'] ?? $response ?? [];
    }

    public function insert(int $supplierId, int $recipeId, array $data): array
    {
        return $this->apiClient->post("/material-suppliers/{$supplierId}/recipes/{$recipeId}/ingredients", $data);
    }

    public function update(int $supplierId, int $recipeId, int $id, array $data): array
    {
        return $this->apiClient->patch("/material-suppliers/{$supplierId}/recipes/{$recipeId}/ingredients/{$id}", $data);
    }

    public function delete(int $supplierId, int $recipeId, int $id): array
    {
        return $this->apiClient->delete("/material-suppliers/{$supplierId}/recipes/{$recipeId}/ingredients/{$id}");
    }
}
