<?php

namespace App\Supplier\app\Http\Controllers\Price;


use App\Supplier\app\Http\Controllers\Controller;
use App\Supplier\app\Services\Catalog\CategoryService;
use App\Supplier\app\Services\Client\ClientService;
use App\Supplier\app\Services\Helpers\AllergenService;
use App\Supplier\app\Services\Price\PriceListService;
use App\Supplier\app\Services\Recipe\RecipeService;
use App\Supplier\core\Support\Route;
use Symfony\Component\HttpFoundation\Request;

class PriceController extends Controller
{

    public function __construct(
        private ClientService $clientService,
        private PriceListService $priceListService,
        private RecipeService $recipeService,
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
        return $this->json($resp);
    }

    #[Route('POST', '/ajax/clients/{clientId:\d+}/price-list/import')]
    public function ajaxImportPriceList($clientId)
    {
        $request = Request::createFromGlobals();
        $data = $this->getJson($request);

        $resp = $this->priceListService->importPriceList($clientId, $data);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    // -----------------------------------------------------------------------
    // Global (all-shops) routes
    // -----------------------------------------------------------------------

    #[Route('GET', '/price-list')]
    public function globalPriceList()
    {
        $data['products'] = $this->priceListService->getPriceListScheduleGlobal();

        $recipes = $this->recipeService->getAll(['is_archived' => 0]);
        $recipeMap = [];
        foreach ($recipes as $recipe) {
            if (!empty($recipe['product_id'])) {
                $pid = (int) $recipe['product_id'];
                if (!isset($recipeMap[$pid])) {
                    $recipeMap[$pid] = $recipe;
                }
            }
        }
        $data['recipeMap'] = $recipeMap;

        $this->view("price_list/index", $data);
    }

    #[Route('POST', '/ajax/products/{productId:\d+}/prices/all-shops')]
    public function ajaxInsertNewPriceAllShops($productId)
    {
        $request = Request::createFromGlobals();
        $data    = $this->getJson($request);

        $resp = $this->priceListService->insertNewPriceAllShops($productId, $data);
        return $this->json($resp, $resp['code'] ?? 201);
    }

    #[Route('DELETE', '/ajax/products/{productId:\d+}/prices/scheduled/{validFrom:[0-9-]+}/all-shops')]
    public function ajaxDeleteAllShops($productId, $validFrom)
    {
        $resp = $this->priceListService->deletePriceAllShops($productId, $validFrom);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    #[Route('POST', '/ajax/price-list/import/all-shops')]
    public function ajaxImportPriceListAllShops()
    {
        $request = Request::createFromGlobals();
        $data    = $this->getJson($request);

        $resp = $this->priceListService->importPriceListAllShops($data);
        return $this->json($resp, $resp['code'] ?? 200);
    }
}