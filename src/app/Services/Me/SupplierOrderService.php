<?php
namespace App\Supplier\app\Services\Me;

use App\Supplier\app\Repositories\Me\SupplierOrderRepository;
use App\Supplier\core\Support\GlobalRegistry;

class SupplierOrderService
{
    public function __construct(
        private SupplierOrderRepository $orderRepository
    ) {}

    public function getAll(array $filters = []): array
    {        
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->getAll($supplierId, $filters);
    }
}
