<?php
namespace App\Supplier\app\Services\Catalog;

use App\Supplier\app\Repositories\Catalog\ProductRepository;
use App\Supplier\core\Support\GlobalRegistry;

class ProductService
{
    public function __construct(
        private ProductRepository $catalogProductRepository
    )
    {
    }

    public function getAll($include = [])
    {
        return $this->catalogProductRepository->getAll(GlobalRegistry::get('user')['supplier_id'], $include);
    }


    public function insert($data)
    {
        $data['supplier_id'] = GlobalRegistry::get('user')['supplier_id'];
        return $this->catalogProductRepository->insert($data);
    }

    public function delete($id)
    {
        return $this->catalogProductRepository->delete($id);
    }

    public function update($id, $data)
    {
        return $this->catalogProductRepository->update($id, $data);
    }

    public function updateSpecification($id, $data)
    {
        return $this->catalogProductRepository->updateSpecification($id, $data);
    }

    public function import($data)
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->catalogProductRepository->import($supplierId, $data);
    }

    public function assignAllergen($productId, $data)
    {
        return $this->catalogProductRepository->assignAllergen($productId, $data);
    }

    public function unassignAllergen($productId, $allergenId)
    {
        return $this->catalogProductRepository->unassignAllergen($productId, $allergenId);
    }

    public function uploadPhoto($productId, array $file): array
    {
        return $this->catalogProductRepository->uploadPhoto($productId, $file);
    }

    public function deletePhoto($productId): array
    {
        return $this->catalogProductRepository->deletePhoto($productId);
    }
}