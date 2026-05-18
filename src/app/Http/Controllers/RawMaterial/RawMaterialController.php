<?php
namespace App\Supplier\app\Http\Controllers\RawMaterial;

use App\Supplier\app\Http\Controllers\Controller;
use App\Supplier\app\Services\Helpers\AllergenService;
use App\Supplier\app\Services\Me\SupplierService;
use App\Supplier\app\Services\RawMaterial\RawMaterialCategoryService;
use App\Supplier\app\Services\RawMaterial\RawMaterialService;
use App\Supplier\app\Services\RawMaterial\UnitService;
use Symfony\Component\HttpFoundation\Request;

/**
 * Kontroler UI modułu "Baza surowców" (supplier portal).
 *
 * Iteracja 2 (UI): widok listy + AJAX list/insert/update + słowniki kategorii i jednostek.
 * Iteracja 4: alergeny — modal szczegółów + assign/unassign.
 * Wszystkie operacje są scoped do zalogowanego dostawcy w warstwie serwisu.
 */
class RawMaterialController extends Controller
{
    public function __construct(
        private RawMaterialService         $rawMaterialService,
        private RawMaterialCategoryService $categoryService,
        private UnitService                $unitService,
        private AllergenService            $allergenService,
        private SupplierService            $supplierService,
    ) {}

    public function index(): void
    {
        $supplier = $this->supplierService->getMe();
        $data = [
            'categories' => $this->categoryService->getAll(),
            'units'      => $this->unitService->getAll(),
            'allergens'  => $this->allergenService->getAll(),
            'currency'   => $supplier ? $supplier->getCurrency() : 'EUR',
        ];

        $this->view('raw_materials/index', $data);
    }

    public function ajaxGetAll()
    {
        $filters = [
            'name'        => $_GET['name']        ?? null,
            'sku'         => $_GET['sku']         ?? null,
            'id_category' => $_GET['id_category'] ?? null,
            'is_archived' => $_GET['is_archived'] ?? null,
        ];

        $items = $this->rawMaterialService->getAll($filters);
        return $this->json(['success' => true, 'data' => $items]);
    }

    public function ajaxGetCategories()
    {
        return $this->json(['success' => true, 'data' => $this->categoryService->getAll()]);
    }

    public function ajaxInsertCategory()
    {
        $request = Request::createFromGlobals();
        $data = $this->getJson($request);

        $resp = $this->categoryService->insert($data);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    public function ajaxUpdateCategory($id)
    {
        $request = Request::createFromGlobals();
        $data = $this->getJson($request);

        $resp = $this->categoryService->update((int)$id, $data);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    public function ajaxDeleteCategory($id)
    {
        $resp = $this->categoryService->delete((int)$id);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    public function ajaxGetUnits()
    {
        return $this->json(['success' => true, 'data' => $this->unitService->getAll()]);
    }

    public function ajaxInsert()
    {
        $request = Request::createFromGlobals();
        $data = $this->getJson($request);

        $resp = $this->rawMaterialService->insert($data);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    public function ajaxUpdate($id)
    {
        $request = Request::createFromGlobals();
        $data = $this->getJson($request);

        $resp = $this->rawMaterialService->update((int)$id, $data);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    // -------------------------------------------------------------------
    // Allergens (iter. 4)
    // -------------------------------------------------------------------

    public function ajaxGetAllergens($id)
    {
        $items = $this->rawMaterialService->getAllergens((int)$id);
        return $this->json(['success' => true, 'data' => $items]);
    }

    public function ajaxAssignAllergen($id)
    {
        $request = Request::createFromGlobals();
        $data = $this->getJson($request);

        $resp = $this->rawMaterialService->assignAllergen((int)$id, $data);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    public function ajaxUnassignAllergen($id, $allergenId)
    {
        $resp = $this->rawMaterialService->unassignAllergen((int)$id, (int)$allergenId);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    // -------------------------------------------------------------------
    // Price history (iter. 5, append-only)
    // -------------------------------------------------------------------

    public function ajaxGetPriceHistory($id)
    {
        $items = $this->rawMaterialService->getPriceHistory((int)$id);
        return $this->json(['success' => true, 'data' => $items]);
    }

    public function ajaxAddPriceEntry($id)
    {
        $request = Request::createFromGlobals();
        $data = $this->getJson($request);

        $resp = $this->rawMaterialService->addPriceEntry((int)$id, $data);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    // -------------------------------------------------------------------
    // Archive / restore / delete (iter. 6)
    // -------------------------------------------------------------------

    public function ajaxArchive($id)
    {
        $resp = $this->rawMaterialService->archive((int)$id);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    public function ajaxRestore($id)
    {
        $resp = $this->rawMaterialService->restore((int)$id);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    public function ajaxDelete($id)
    {
        $resp = $this->rawMaterialService->delete((int)$id);
        return $this->json($resp, $resp['code'] ?? 200);
    }
}

