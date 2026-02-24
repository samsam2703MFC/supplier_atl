<?php
namespace App\Supplier\app\Services\Catalog;

use App\Supplier\app\Repositories\Catalog\CategoryRepository;
use App\Supplier\core\Support\GlobalRegistry;

class CategoryService
{
    public function __construct(
        private CategoryRepository $catalogCategoryRepository
    )
    {
    }

    public function getAll()
    {
        return $this->catalogCategoryRepository->getAll(GlobalRegistry::get('user')['supplier_id']);
    }

    public function insert($data)
    {
        return $this->catalogCategoryRepository->insert(GlobalRegistry::get('user')['supplier_id'], $data);
    }

    public function update($id, $data)
    {
        return $this->catalogCategoryRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->catalogCategoryRepository->delete($id);
    }
}
