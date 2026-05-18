<?php
namespace App\Supplier\app\Services\Me;

use App\Supplier\app\Repositories\Catalog\CategoryRepository;
use App\Supplier\app\Repositories\Ingredient\IngredientRepository;
use App\Supplier\app\Repositories\Me\SupplierRepository;
use App\Supplier\core\Support\GlobalRegistry;

class SupplierService
{
    public function __construct(
        private SupplierRepository $supplierRepository
    )
    {
    }

    public function getMe()
    {
        return $this->supplierRepository->getMe(GlobalRegistry::get('user')['supplier_id']);
    }

    public function isIntegrated(): bool
    {
        return (bool)(GlobalRegistry::get('user')['is_integrated'] ?? false);
    }

    public function updateProfile(array $data)
    {
        $id = GlobalRegistry::get('user')['supplier_id'];
        return $this->supplierRepository->updateProfile($id, $data);
    }

    public function uploadLogo(array $file)
    {
        $id = GlobalRegistry::get('user')['supplier_id'];
        return $this->supplierRepository->uploadLogo($id, $file);
    }

    public function getLaborDefaults(): array
    {
        $id = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->supplierRepository->getLaborDefaults($id);
    }

    public function updateLaborDefaults(array $data): array
    {
        $id = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->supplierRepository->updateLaborDefaults($id, $data);
    }
}