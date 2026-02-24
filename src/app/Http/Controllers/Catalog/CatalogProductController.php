<?php

namespace App\Supplier\app\Http\Controllers\Catalog;

use App\Supplier\app\Http\Controllers\Controller;
use App\Supplier\app\Services\Catalog\ProductService;
use App\Supplier\core\Support\Route;
use Symfony\Component\HttpFoundation\Request;

class CatalogProductController extends Controller
{

    public function __construct(
        private ProductService $catalogProductService
    ) {}

    #[Route('GET', '/ajax/catalog/products')]
    public function ajaxGetAll()
    {
        $include = explode(",", $_GET['include']) ?? [];
        $response = $this->catalogProductService->getAll($include);
        return $this->json($response);
    }

    #[Route('POST', '/ajax/catalog/products')]
    public function ajaxInsert()
    {
        $request = Request::createFromGlobals();
        $data = $this->getJson($request);

        $resp = $this->catalogProductService->insert($data);
        return $this->json($resp, $resp['code']);
    }

    #[Route('PUT', '/ajax/catalog/products/{id:\d+}')]
    public function ajaxUpdate($id)
    {
        $request = Request::createFromGlobals();
        $data = $this->getJson($request);

        $resp = $this->catalogProductService->update($id, $data);
        return $this->json($resp, $resp['code']);
    }

    #[Route('POST', '/ajax/catalog/products/{id:\d+}/specification')]
    public function ajaxUpdateSpecification($id)
    {
        $request = Request::createFromGlobals();
        $data = $this->getJson($request);

        $resp = $this->catalogProductService->updateSpecification($id, $data);
        return $this->json($resp, $resp['code']);
    }

    #[Route('POST', '/ajax/catalog/products/import')]
    public function ajaxImport()
    {
        $request = Request::createFromGlobals();
        $data = $this->getJson($request);

        $resp = $this->catalogProductService->import($data);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    #[Route('DELETE', '/ajax/catalog/products/{id:\d+}')]
    public function ajaxDelete($id)
    {
        $response = $this->catalogProductService->delete($id);
        return $this->json($response);
    }

    #[Route('POST', '/ajax/catalog/products/{productId:\d+}/allergens')]
    public function ajaxAssignAllergen($productId)
    {
        $request = Request::createFromGlobals();
        $data = $this->getJson($request);

        $resp = $this->catalogProductService->assignAllergen($productId, $data);
        return $this->json($resp, $resp['code']);
    }

    #[Route('DELETE', '/ajax/catalog/products/{productId:\d+}/allergens/{allergenId:\d+}')]
    public function ajaxUnassignAllergen($productId, $allergenId)
    {
        $response = $this->catalogProductService->unassignAllergen($productId, $allergenId);
        return $this->json($response);
    }
}