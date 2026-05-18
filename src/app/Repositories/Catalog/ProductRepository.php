<?php
namespace App\Supplier\app\Repositories\Catalog;


use App\Supplier\app\Models\Catalog\CategoryModel;
use App\Supplier\app\Models\Catalog\ProductModel;
use App\Supplier\core\Http\ApiClient;

class ProductRepository
{
    public function __construct(
        private ApiClient $apiClient
    )
    {
    }

    public function getAll($supplierId, $include = [])
    {

        $queryStr = $include ? "?include=" . implode(",", $include) : "";


        $response = $this->apiClient->get("/material-suppliers/$supplierId/catalog/products$queryStr");

        $objects = [];
        if(isset($response['data'])){
            foreach ($response['data'] as $object){
                $objects[] = new ProductModel($object);
            }
        }

        return $objects;
    }


    public function insert($data)
    {
        return $this->apiClient->post("/material-suppliers/catalog/products", $data);
    }

    public function update($id, $data)
    {
        return $this->apiClient->patch("/material-suppliers/catalog/products/{$id}", $data);
    }

    public function updateSpecification($id, $data)
    {
        return $this->apiClient->post("/material-suppliers/catalog/products/{$id}/specification", $data);
    }

    public function import($supplierId, $data)
    {
        return $this->apiClient->post("/material-suppliers/{$supplierId}/catalog/products/import", $data);
    }

    public function delete($id)
    {
        return $this->apiClient->delete("/material-suppliers/catalog/products/{$id}");
    }


    public function assignAllergen($productId, $data)
    {
        return $this->apiClient->post("/material-suppliers/catalog/products/$productId/allergens", $data);
    }

    public function unassignAllergen($productId, $allergenId)
    {
        return $this->apiClient->delete("/material-suppliers/catalog/products/$productId/allergens/{$allergenId}");
    }

    public function uploadPhoto($productId, array $file): array
    {

        return $this->apiClient->postMultipart(
            "/material-suppliers/catalog/products/{$productId}/photo",
            [],
            ['photo' => $file]
        );
    }

    public function deletePhoto($productId): array
    {
        return $this->apiClient->delete("/material-suppliers/catalog/products/{$productId}/photo");
    }
}