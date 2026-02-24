<?php

namespace App\Supplier\app\Http\Controllers\Ingredient;


use App\Supplier\app\Http\Controllers\Controller;
use App\Supplier\app\Services\Client\ClientService;
use App\Supplier\app\Services\Ingredient\IngredientService;
use App\Supplier\core\Support\Route;
use Symfony\Component\HttpFoundation\Request;

class IngredientController extends Controller
{

    public function __construct(
        private IngredientService $ingredientService
    ) {}


    #[Route('POST', '/ajax/ingredients')]
    public function ajaxInsert()
    {
        $request = Request::createFromGlobals();
        $data = $this->getJson($request);

        $resp = $this->ingredientService->insert($data);
        return $this->json($resp, $resp['code']);
    }

    #[Route('GET', '/ajax/catalog/products/{productId:\d+}/ingredients')]
    public function ajaxGetByProduct($productId)
    {
        $response = $this->ingredientService->getByProduct($productId);
        return $this->json($response);
    }

    #[Route('GET', '/ajax/ingredients')]
    public function ajaxGetAll()
    {
        $response = $this->ingredientService->getAll();
        return $this->json($response);
    }

    #[Route('POST', '/ajax/catalog/products/{productId:\d+}/ingredients')]
    public function ajaxAssignToProduct($productId)
    {
        $request = Request::createFromGlobals();
        $data = $this->getJson($request);

        $resp = $this->ingredientService->assignToProduct($productId, $data);
        return $this->json($resp, $resp['code']);
    }

    #[Route('DELETE', '/ajax/catalog/products/{productId:\d+}/ingredients/{ingredientId:\d+}')]
    public function ajaxUnassignFromProduct($productId, $ingredientId)
    {
        $request = Request::createFromGlobals();
        $data = $this->getJson($request);

        $resp = $this->ingredientService->unassignFromProduct($productId, $ingredientId);
        return $this->json($resp);
    }


}