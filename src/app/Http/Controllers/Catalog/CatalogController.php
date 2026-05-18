<?php

namespace App\Supplier\app\Http\Controllers\Catalog;


use App\Supplier\app\Http\Controllers\Controller;
use App\Supplier\app\Services\Catalog\CategoryService;
use App\Supplier\app\Services\Helpers\AllergenService;
use App\Supplier\app\Services\Ingredient\IngredientService;
use App\Supplier\app\Services\Me\SupplierService;
use App\Supplier\core\Support\Route;

class CatalogController extends Controller
{

    public function __construct(
        private AllergenService $allergenService,
        private SupplierService $supplierService
    ) {}

    #[Route('GET', '/catalog')]
    public function index()
    {
        $data['allergens'] = $this->allergenService->getAll();

        try {
            $supplier = $this->supplierService->getMe();
            $data['supplierName'] = $supplier ? $supplier->getName() : '';
        } catch (\Exception $e) {
            $data['supplierName'] = '';
        }

        $this->view("catalog/catalog", $data);
    }
}