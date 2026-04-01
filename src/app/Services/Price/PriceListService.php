<?php
namespace App\Supplier\app\Services\Price;

use App\Supplier\app\Repositories\Catalog\CategoryRepository;
use App\Supplier\app\Repositories\Ingredient\IngredientRepository;
use App\Supplier\app\Repositories\Price\PriceListRepository;
use App\Supplier\core\Support\GlobalRegistry;

class PriceListService
{
    public function __construct(
        private PriceListRepository $pricelistRepository
    )
    {
    }

    public function getLatestPriceList($clientId)
    {
        return $this->pricelistRepository->getLatestByShop(GlobalRegistry::get('user')['supplier_id'], $clientId);
    }

    public function getCurrentPriceList($clientId)
    {
        return $this->pricelistRepository->getCurrentByShop(GlobalRegistry::get('user')['supplier_id'], $clientId);
    }

    public function getPriceListSchedule($clientId)
    {
        return $this->pricelistRepository->getPriceListSchedule(GlobalRegistry::get('user')['supplier_id'], $clientId);
    }

    public function insertNewPrice($clientId, $productId, $data)
    {
        return $this->pricelistRepository->insertNewPrice(GlobalRegistry::get('user')['supplier_id'], $clientId, $productId, $data);
    }

    public function deletePrice($clientId, $productId, $priceId)
    {
        return $this->pricelistRepository->deletePrice(GlobalRegistry::get('user')['supplier_id'], $clientId, $productId, $priceId);
    }

    public function importPriceList($clientId, $data)
    {
        return $this->pricelistRepository->importPriceList(GlobalRegistry::get('user')['supplier_id'], $clientId, $data);
    }
}