<?php

namespace App\Supplier\app\Http\Controllers\Orders;

use App\Supplier\app\Http\Controllers\Controller;
use App\Supplier\app\Services\Me\SupplierOrderService;
use App\Supplier\core\Support\Route;

class OrdersController extends Controller
{
    public function __construct(
        private SupplierOrderService $orderService
    ) {}

    #[Route('GET', '/orders')]
    public function index()
    {
        $filters = [];

        if (!empty($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }
        if (!empty($_GET['from'])) {
            $filters['from'] = $_GET['from'];
        }
        if (!empty($_GET['to'])) {
            $filters['to'] = $_GET['to'];
        }

        $data['orders'] = $this->orderService->getAll($filters);

        $data['current_path'] = '/orders';
        $data['filters'] = $filters;

        $this->view("orders/index", $data);
    }
}
