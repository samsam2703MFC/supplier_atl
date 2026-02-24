<?php

namespace App\Supplier\app\Http\Controllers\Price;


use App\Supplier\app\Http\Controllers\Controller;
use App\Supplier\app\Services\Catalog\CategoryService;
use App\Supplier\app\Services\Client\ClientService;
use App\Supplier\app\Services\Helpers\AllergenService;
use App\Supplier\app\Services\Price\PriceListService;
use App\Supplier\core\Support\Route;
use Symfony\Component\HttpFoundation\Request;

class PriceController extends Controller
{

    public function __construct(
        private ClientService $clientService,
        private PriceListService $priceListService
    ) {}

    #[Route('GET', '/clients/{clientId:\d+}/price-list')]
    public function priceList($clientId)
    {
        $data['products'] = $this->priceListService->getPriceListSchedule($clientId);
        $data['client'] = $this->clientService->getById($clientId);

        $this->view("client/price_list", $data);
    }

    #[Route('POST', '/ajax/clients/{clientId:\d+}/products/{productId:\d+}/prices')]
    public function ajaxInsertNewPrice($clientId, $productId)
    {
        $request = Request::createFromGlobals();
        $data = $this->getJson($request);

        $resp = $this->priceListService->insertNewPrice($clientId, $productId, $data);
        return $this->json($resp, $resp['code']);
    }

    #[Route('POST', '/ajax/clients/{clientId:\d+}/products/{productId:\d+}/prices/{priceId:\d+}')]
    public function ajaxDelete($clientId, $productId, $priceId)
    {
        $request = Request::createFromGlobals();
        $data = $this->getJson($request);

        $resp = $this->priceListService->deletePrice($clientId, $productId, $priceId);
        return $this->json($resp, $resp['code']);
    }
}