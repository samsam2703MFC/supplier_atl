<?php
namespace App\Supplier\app\Repositories\Catalog;


use App\Supplier\app\Models\Catalog\CategoryModel;
use App\Supplier\core\Http\ApiClient;

class CategoryRepository
{
    public function __construct(
        private ApiClient $apiClient
    )
    {
    }

    public function getAll($supplierId)
    {

        $response = $this->apiClient->get("/material-suppliers/$supplierId/catalog/categories");

        $objects = [];
        if(isset($response['data'])){
            foreach ($response['data'] as $object){
                $objects[] = new CategoryModel($object);
            }
        }

        return $objects;
    }



}