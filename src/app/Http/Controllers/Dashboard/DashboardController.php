<?php

namespace App\Supplier\app\Http\Controllers\Dashboard;


use App\Supplier\app\Http\Controllers\Controller;
use App\Supplier\app\Services\Me\AnalyticsService;
use App\Supplier\app\Services\Me\ComplaintService;
use App\Supplier\core\Support\Route;

class DashboardController extends Controller
{

    public function __construct(
        private AnalyticsService $analyticsService,
        private ComplaintService $complaintService
    ) {}

    #[Route('GET', '/dashboard')]
    public function index()
    {
        $data['kpi']              = $this->analyticsService->getSummary();
        $data['top_products']     = $this->analyticsService->getTopProducts();
        $data['shop_revenue']     = $this->analyticsService->getRevenueByShop();
        $data['open_complaints']  = $this->complaintService->getAll(['status' => 'NEW,IN_REVIEW']);
        $data['current_path']     = '/dashboard';

        $this->view("dashboard/dashboard", $data);
    }
}