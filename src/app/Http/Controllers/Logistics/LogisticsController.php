<?php

namespace App\Supplier\app\Http\Controllers\Logistics;

use App\Supplier\app\Http\Controllers\Controller;
use App\Supplier\app\Services\Me\LogisticsService;
use App\Supplier\core\Support\Route;

class LogisticsController extends Controller
{
    public function __construct(
        private LogisticsService $logisticsService
    ) {}

    #[Route('GET', '/logistics')]
    public function index(): void
    {
        $deliveries = $this->logisticsService->getUpcomingDeliveries();

        $today    = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $weekEnd  = date('Y-m-d', strtotime('sunday this week'));

        $grouped = [
            'today'    => [],
            'tomorrow' => [],
            'week'     => [],
            'later'    => [],
        ];

        foreach ($deliveries as $delivery) {
            $d = $delivery['expected_date'] ?? null;

            if ($d === null || $d === '') {
                $grouped['later'][] = $delivery;
            } elseif ($d === $today) {
                $grouped['today'][] = $delivery;
            } elseif ($d === $tomorrow) {
                $grouped['tomorrow'][] = $delivery;
            } elseif ($d <= $weekEnd) {
                $grouped['week'][] = $delivery;
            } else {
                $grouped['later'][] = $delivery;
            }
        }

        $data['grouped']      = $grouped;
        $data['total']        = count($deliveries);
        $data['current_path'] = '/logistics';

        $this->view('logistics/index', $data);
    }
}
