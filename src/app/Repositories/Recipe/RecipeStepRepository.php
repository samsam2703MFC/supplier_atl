<?php
namespace App\Supplier\app\Repositories\Recipe;

use App\Supplier\core\Http\ApiClient;

/**
 * Klient HTTP dla zasobu recipe_steps w API (scoped do supplierId + recipeId).
 */
class RecipeStepRepository
{
    public function __construct(
        private ApiClient $apiClient
    ) {}

    private function base(int $supplierId, int $recipeId): string
    {
        return "/material-suppliers/{$supplierId}/recipes/{$recipeId}/steps";
    }

    // -----------------------------------------------------------------------
    //  Read
    // -----------------------------------------------------------------------

    public function getAll(int $supplierId, int $recipeId): array
    {
        $response = $this->apiClient->get($this->base($supplierId, $recipeId));
        return $response['data'] ?? [];
    }

    // -----------------------------------------------------------------------
    //  CRUD
    // -----------------------------------------------------------------------

    public function insert(int $supplierId, int $recipeId, array $data): array
    {
        return $this->apiClient->post($this->base($supplierId, $recipeId), $data);
    }

    public function update(int $supplierId, int $recipeId, int $id, array $data): array
    {
        return $this->apiClient->patch($this->base($supplierId, $recipeId) . "/{$id}", $data);
    }

    public function delete(int $supplierId, int $recipeId, int $id): array
    {
        return $this->apiClient->delete($this->base($supplierId, $recipeId) . "/{$id}");
    }

    // -----------------------------------------------------------------------
    //  Reorder
    // -----------------------------------------------------------------------

    public function reorder(int $supplierId, int $recipeId, array $items): array
    {
        return $this->apiClient->patch(
            $this->base($supplierId, $recipeId) . '/reorder',
            ['items' => $items]
        );
    }

    // -----------------------------------------------------------------------
    //  Photos
    // -----------------------------------------------------------------------

    public function uploadPhoto(int $supplierId, int $recipeId, int $stepId, int $slot, array $file): array
    {
        return $this->apiClient->postMultipart(
            $this->base($supplierId, $recipeId) . "/{$stepId}/photos/{$slot}",
            [],
            ['photo' => $file]
        );
    }

    public function deletePhoto(int $supplierId, int $recipeId, int $stepId, int $slot): array
    {
        return $this->apiClient->delete(
            $this->base($supplierId, $recipeId) . "/{$stepId}/photos/{$slot}"
        );
    }
}
