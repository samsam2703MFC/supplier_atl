<?php
namespace App\Supplier\app\Services\RawMaterial;

use App\Supplier\app\Repositories\RawMaterial\RawMaterialCategoryRepository;
use App\Supplier\core\Support\GlobalRegistry;

class RawMaterialCategoryService
{
    public function __construct(
        private RawMaterialCategoryRepository $repository
    ) {}

    public function getAll(): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->repository->getAll($supplierId);
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
}

