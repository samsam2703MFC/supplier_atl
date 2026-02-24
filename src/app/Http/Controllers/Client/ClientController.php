<?php

namespace App\Supplier\app\Http\Controllers\Client;


use App\Supplier\app\Http\Controllers\Controller;
use App\Supplier\app\Services\Client\ClientService;
use App\Supplier\core\Support\Route;

class ClientController extends Controller
{

    public function __construct(
        private ClientService $clientService
    ) {}

    #[Route('GET', '/clients')]
    public function index()
    {
        $data['clients'] = $this->clientService->getAll();

        $this->view("client/index", $data);
    }


}