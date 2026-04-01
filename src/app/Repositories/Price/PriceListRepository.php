<?php

namespace App\Supplier\app\Repositories\Price;

use App\Supplier\app\Models\Price\PriceListItemModel;
use App\Supplier\core\Http\ApiClient;

class PriceListRepository
{

    public function __construct(
        private ApiClient $apiClient
    )
    {
    }

    public function getCurrentByShop($supplierId, $clientId)
    {
        $response = $this->apiClient->get("/material-suppliers/$supplierId/shops/{$clientId}/price-lists/current");

        $objects = [];
        if(isset($response['data'])){
            foreach ($response['data'] as $object){
                $objects[] = new PriceListItemModel($object);
            }
        }

        return $objects;

    }

    public function getPriceListSchedule($supplierId, $clientId)
    {
        $response = $this->apiClient->get("/material-suppliers/$supplierId/shops/{$clientId}/price-lists/schedule");

        $objects = [];
        if(isset($response['data'])){
            foreach ($response['data'] as $object){
                $objects[] = new PriceListItemModel($object);
            }
        }

        return $objects;

    }

    public function getLatestByShop($supplierId, $clientId)
    {
        $response = $this->apiClient->get("/material-suppliers/$supplierId/shops/{$clientId}/price-lists/latest");

        $objects = [];
        if(isset($response['data'])){
            foreach ($response['data'] as $object){
                $objects[] = new PriceListItemModel($object);
            }
        }

        return $objects;

    }

    public function insertNewPrice($supplierId, $clientId, $productId, $data)
    {
        return $this->apiClient->post("/material-suppliers/$supplierId/shops/$clientId/products/$productId/price", $data);
    }

    public function deletePrice($supplierId, $clientId, $productId, $priceId)
    {
        return $this->apiClient->delete("/material-suppliers/$supplierId/shops/$clientId/price-lists/$priceId");
    }

    public function importPriceList($supplierId, $clientId, $data)
    {
        return $this->apiClient->post("/material-suppliers/$supplierId/shops/$clientId/price-lists/import", $data);
    }
}