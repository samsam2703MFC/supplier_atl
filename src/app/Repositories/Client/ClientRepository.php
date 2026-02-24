<?php
namespace App\Supplier\app\Repositories\Client;


use App\Supplier\app\Models\Catalog\AllergenModel;
use App\Supplier\app\Models\Catalog\CategoryModel;
use App\Supplier\app\Models\Client\ShopModel;
use App\Supplier\core\Http\ApiClient;

class ClientRepository
{
    public function __construct(
        private ApiClient $apiClient
    )
    {
    }

    public function getAll()
    {
        $response = $this->apiClient->get("/shops");

        $objects = [];
        if(isset($response['data'])){
            foreach ($response['data'] as $object){
                $objects[] = new ShopModel($object);
            }
        }

        return $objects;
    }

    public function getById($id)
    {
        $resp = $this->apiClient->get('/shops/' . $id);
        return new ShopModel($resp['data']) ?? null;
    }
}