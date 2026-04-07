<?php
namespace App\Supplier\app\Http\Controllers\Me;

use App\Supplier\app\Http\Controllers\Controller;
use App\Supplier\app\Services\Me\OrderConfigService;
use App\Supplier\core\Support\Route;

class OrderConfigController extends Controller
{
    public function __construct(
        private OrderConfigService $orderConfigService
    ) {}

    #[Route('GET', '/me/order-config')]
    public function index(): void
    {
        $data['config'] = $this->orderConfigService->getConfig();
        $this->view('me/order-config', $data);
    }

    #[Route('POST', '/me/order-config')]
    public function save(): void
    {
        $saved = $this->orderConfigService->saveConfig($_POST);

        if ($saved) {
            $this->successes[] = 'config_saved_successfully';
        } else {
            $this->errors[] = 'config_save_failed';
        }

        $data['config'] = $this->orderConfigService->getConfig();
        $this->view('me/order-config', $data);
    }
}

