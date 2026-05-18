<?php
namespace App\Supplier\app\Services\RawMaterial;

use App\Supplier\app\Repositories\RawMaterial\UnitRepository;

class UnitService
{
    public function __construct(
        private UnitRepository $repository
    ) {}

    public function getAll(): array
    {
        return $this->repository->getAll();
    }
}

