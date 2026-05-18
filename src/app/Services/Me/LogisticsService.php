<?php
namespace App\Supplier\app\Services\Me;

use App\Supplier\app\Repositories\Me\LogisticsRepository;
use App\Supplier\core\Support\GlobalRegistry;

class LogisticsService
{
    public function __construct(
        private LogisticsRepository $logisticsRepository
    ) {}

    public function getUpcomingDeliveries(): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->logisticsRepository->getUpcomingDeliveries($supplierId);
    }
}
