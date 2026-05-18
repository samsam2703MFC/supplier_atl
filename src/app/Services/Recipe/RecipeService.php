<?php
namespace App\Supplier\app\Services\Recipe;

use App\Supplier\core\Support\GlobalRegistry;
use App\Supplier\app\Repositories\Recipe\RecipeRepository;

/**
 * BFF serwis receptur — deleguje do repozytorium HTTP z wstrzyknięciem supplierId.
 */
class RecipeService
{
    public function __construct(
        private RecipeRepository $repository
    ) {}

    public function getAll(array $filters = []): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->repository->getAll($supplierId, $filters);
    }

    public function getById(int $id): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->repository->getById($supplierId, $id);
    }

    public function insert(array $data): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->repository->insert($supplierId, $data);
    }

    public function update(int $id, array $data): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->repository->update($supplierId, $id, $data);
    }

    public function delete(int $id): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->repository->delete($supplierId, $id);
    }

    public function archive(int $id): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->repository->archive($supplierId, $id);
    }

    public function restore(int $id): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->repository->restore($supplierId, $id);
    }

    public function recalculateOverhead(int $id): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->repository->recalculateOverhead($supplierId, $id);
    }
}
