<?php

namespace App\Supplier\app\Http\Controllers\Analytics;

use App\Supplier\app\Http\Controllers\Controller;
use App\Supplier\app\Services\Me\AnalyticsService;
use App\Supplier\core\Support\Route;

class AnalyticsController extends Controller
{
    public function __construct(
        private AnalyticsService $analyticsService
    ) {}

    #[Route('GET', '/analytics')]
    public function index()
    {
        $params = [];

        if (!empty($_GET['from'])) {
            $params['from'] = $_GET['from'];
        }
        if (!empty($_GET['to'])) {
            $params['to'] = $_GET['to'];
        }

        // Default range: current year
        if (empty($params['from'])) {
            $params['from'] = date('Y-01-01');
        }
        if (empty($params['to'])) {
            $params['to'] = date('Y-m-d');
        }

        $data['revenue']        = $this->analyticsService->getRevenue($params);
        $data['top_products']   = $this->analyticsService->getTopProducts($params);
        $data['shop_revenue']   = $this->analyticsService->getRevenueByShop($params);
        $data['params']         = $params;
        $data['current_path']   = '/analytics';

        $this->view("analytics/index", $data);
    }

    #[Route('GET', '/analytics/raw-material-sales')]
    public function rawMaterialSales()
    {
        $shopId   = isset($_GET['shop']) && $_GET['shop'] !== '' ? (int) $_GET['shop']  : null;
        $dateFrom = isset($_GET['from']) && $_GET['from'] !== '' ? $_GET['from']        : date('Y-m-01');
        $dateTo   = isset($_GET['to'])   && $_GET['to']   !== '' ? $_GET['to']          : date('Y-m-d');

        $shops = $this->analyticsService->getShops();

        $sales = [];
        if ($shopId !== null) {
            $sales = $this->analyticsService->getRawMaterialSales([
                'shop' => $shopId,
                'from' => $dateFrom,
                'to'   => $dateTo,
            ]);
        }

        $data['shops']        = $shops;
        $data['sales']        = $sales;
        $data['selected_shop'] = $shopId;
        $data['params']       = ['shop' => $shopId, 'from' => $dateFrom, 'to' => $dateTo];
        $data['current_path'] = '/analytics/raw-material-sales';

        $this->view("analytics/raw_material_sales", $data);
    }

    #[Route('GET', '/analytics/raw-material-sales-all-shops')]
    public function rawMaterialSalesAllShops()
    {
        $dateFrom = isset($_GET['from']) && $_GET['from'] !== '' ? $_GET['from'] : date('Y-m-01');
        $dateTo   = isset($_GET['to'])   && $_GET['to']   !== '' ? $_GET['to']   : date('Y-m-d');
        $sales = $this->analyticsService->getRawMaterialSalesAllShops([
            'from' => $dateFrom,
            'to'   => $dateTo,
        ]);
        $data['sales']        = $sales;
        $data['params']       = ['from' => $dateFrom, 'to' => $dateTo];
        $data['current_path'] = '/analytics/raw-material-sales-all-shops';
        $this->view("analytics/raw_material_sales_all_shops", $data);
    }
}
