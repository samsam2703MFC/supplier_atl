<?php
namespace App\Supplier\app\Services\Recipe;

use App\Supplier\app\Repositories\Recipe\RecipeStepRepository;
use App\Supplier\core\Support\GlobalRegistry;

/**
 * BFF serwis kroków receptury — deleguje do repozytorium HTTP z wstrzyknięciem supplierId.
 */
class RecipeStepService
{
    public function __construct(
        private RecipeStepRepository $repository
    ) {}

    // -----------------------------------------------------------------------
    //  Helpers
    // -----------------------------------------------------------------------

    private function supplierId(): int
    {
        return (int) GlobalRegistry::get('user')['supplier_id'];
    }

    // -----------------------------------------------------------------------
    //  Read
    // -----------------------------------------------------------------------

    public function getAll(int $recipeId): array
    {
        return $this->repository->getAll($this->supplierId(), $recipeId);
    }

    // -----------------------------------------------------------------------
    //  CRUD
    // -----------------------------------------------------------------------

    public function insert(int $recipeId, array $data): array
    {
        return $this->repository->insert($this->supplierId(), $recipeId, $data);
    }

    public function update(int $recipeId, int $id, array $data): array
    {
        return $this->repository->update($this->supplierId(), $recipeId, $id, $data);
    }

    public function delete(int $recipeId, int $id): array
    {
        return $this->repository->delete($this->supplierId(), $recipeId, $id);
    }

    // -----------------------------------------------------------------------
    //  Reorder
    // -----------------------------------------------------------------------

    public function reorder(int $recipeId, array $items): array
    {
        return $this->repository->reorder($this->supplierId(), $recipeId, $items);
    }

    // -----------------------------------------------------------------------
    //  Photos
    // -----------------------------------------------------------------------

    public function uploadPhoto(int $recipeId, int $stepId, int $slot, array $file): array
    {
        return $this->repository->uploadPhoto($this->supplierId(), $recipeId, $stepId, $slot, $file);
    }

    public function deletePhoto(int $recipeId, int $stepId, int $slot): array
    {
        return $this->repository->deletePhoto($this->supplierId(), $recipeId, $stepId, $slot);
    }
}
