<?php
namespace App\Supplier\app\Services\RawMaterial;

use App\Supplier\app\Repositories\RawMaterial\RawMaterialRepository;
use App\Supplier\core\Support\GlobalRegistry;

/**
 * Warstwa serwisowa BFF (Backend For Frontend) — wstrzykuje supplierId
 * z aktualnie zalogowanego użytkownika i deleguje do repository.
 *
 * Logika domenowa (walidacja, ownership, ograniczenia) pozostaje w API
 * (App\Api\Services\Material\Supplier\RawMaterial\RawMaterialService).
 */
class RawMaterialService
{
    public function __construct(
        private RawMaterialRepository $repository
    ) {}

    public function getAll(array $filters = []): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->repository->getAll($supplierId, $filters);
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

    // -------------------------------------------------------------------
    // Allergens (iter. 4)
    // -------------------------------------------------------------------

    public function getAllergens(int $id): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->repository->getAllergens($supplierId, $id);
    }

    public function assignAllergen(int $id, array $data): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->repository->assignAllergen($supplierId, $id, $data);
    }

    public function unassignAllergen(int $id, int $allergenId): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->repository->unassignAllergen($supplierId, $id, $allergenId);
    }

    // -------------------------------------------------------------------
    // Price history (iter. 5, append-only)
    // -------------------------------------------------------------------

    public function getPriceHistory(int $id): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->repository->getPriceHistory($supplierId, $id);
    }

    public function addPriceEntry(int $id, array $data): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->repository->addPriceEntry($supplierId, $id, $data);
    }

    // -------------------------------------------------------------------
    // Archive / restore / delete (iter. 6)
    // -------------------------------------------------------------------

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

    public function delete(int $id): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->repository->delete($supplierId, $id);
    }
}

