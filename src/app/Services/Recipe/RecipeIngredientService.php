<?php
namespace App\Supplier\app\Services\Recipe;

use App\Supplier\core\Support\GlobalRegistry;
use App\Supplier\app\Repositories\Recipe\RecipeIngredientRepository;

/**
 * BFF serwis składników receptury.
 */
class RecipeIngredientService
{
    public function __construct(
        private RecipeIngredientRepository $repository
    ) {}

    public function getAll(int $recipeId): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->repository->getAll($supplierId, $recipeId);
    }

    public function getSummary(int $recipeId): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->repository->getSummary($supplierId, $recipeId);
    }

    public function insert(int $recipeId, array $data): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->repository->insert($supplierId, $recipeId, $data);
    }

    public function update(int $recipeId, int $id, array $data): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->repository->update($supplierId, $recipeId, $id, $data);
    }

    public function delete(int $recipeId, int $id): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->repository->delete($supplierId, $recipeId, $id);
    }
}
