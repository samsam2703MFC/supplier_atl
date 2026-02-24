<?php
namespace App\Supplier\app\Services\Ingredient;

use App\Supplier\app\Repositories\Catalog\CategoryRepository;
use App\Supplier\app\Repositories\Ingredient\IngredientRepository;
use App\Supplier\core\Support\GlobalRegistry;

class IngredientService
{
    public function __construct(
        private IngredientRepository $ingredientRepository
    )
    {
    }

    public function insert($data)
    {
        $data['supplier_id'] = GlobalRegistry::get('user')['supplier_id'];
        return $this->ingredientRepository->insert($data);
    }

    public function getByProduct($productId)
    {
        return $this->ingredientRepository->getByProduct($productId);
    }

    public function getAll()
    {
        return $this->ingredientRepository->getAll();
    }

    public function assignToProduct($productId, $data)
    {
        return $this->ingredientRepository->assignToProduct($productId, $data);
    }

    public function unassignFromProduct($productId, $ingredientId)
    {
        return $this->ingredientRepository->unassignFromProduct($productId, $ingredientId);
    }




}