<?php

namespace App\Supplier\app\Repositories\Ingredient;

use App\Supplier\app\Models\Ingredient\IngredientModel;
use App\Supplier\core\Http\ApiClient;

class IngredientRepository
{
    public function __construct(
        private ApiClient $apiClient
    )
    {}

    public function insert($data)
    {
        return $this->apiClient->post("/material-suppliers/ingredients", $data);
    }

    public function getByProduct($productId)
    {
        $response = $this->apiClient->get("/material-suppliers/products/{$productId}/ingredients");

        $objects = [];
        if(isset($response['data'])) {
            foreach ($response['data'] as $object) {
                $objects[] = new IngredientModel($object);
            }
        }
        return $objects;
    }

    public function getAll()
    {
        $response = $this->apiClient->get("/material-suppliers/ingredients");

        $objects = [];
        if(isset($response['data'])) {
            foreach ($response['data'] as $object) {
                $objects[] = new IngredientModel($object);
            }
        }
        return $objects;
    }

    public function assignToProduct($productId, $data)
    {
        return $this->apiClient->post("/material-suppliers/products/{$productId}/ingredients", $data);
    }

    public function unassignFromProduct($productId, $ingredientId)
    {
        return $this->apiClient->delete("/material-suppliers/products/{$productId}/ingredients/{$ingredientId}");
    }
}