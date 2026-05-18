<?php
namespace App\Supplier\app\Services\Me;

use App\Supplier\app\Repositories\Me\AnalyticsRepository;
use App\Supplier\core\Support\GlobalRegistry;

class AnalyticsService
{
    public function __construct(
        private AnalyticsRepository $analyticsRepository
    ) {}

    public function getSummary(): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->analyticsRepository->getSummary($supplierId);
    }

    public function getRevenue(array $params = []): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->analyticsRepository->getRevenue($supplierId, $params);
    }

    public function getTopProducts(array $params = []): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->analyticsRepository->getTopProducts($supplierId, $params);
    }
    public function getRevenueByShop(array $params = []): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->analyticsRepository->getRevenueByShop($supplierId, $params);
    }

    public function getShops(): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->analyticsRepository->getShops($supplierId);
    }

    public function getRawMaterialSales(array $params = []): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->analyticsRepository->getRawMaterialSales($supplierId, $params);
    }

    public function getRawMaterialSalesAllShops(array $params = []): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->analyticsRepository->getRawMaterialSalesAllShops($supplierId, $params);
    }
}
