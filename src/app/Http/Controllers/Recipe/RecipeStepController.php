<?php
namespace App\Supplier\app\Http\Controllers\Recipe;

use App\Supplier\app\Http\Controllers\Controller;
use App\Supplier\app\Services\Recipe\RecipeService;
use App\Supplier\app\Services\Recipe\RecipeStepService;
use Symfony\Component\HttpFoundation\Request;

/**
 * Kontroler widoku i AJAX kroków przygotowania receptury.
 *
 * Widok: GET /recipes/{recipeId}/steps
 * AJAX:  /ajax/recipes/{recipeId}/steps[/...]
 */
class RecipeStepController extends Controller
{
    public function __construct(
        private RecipeStepService $stepService,
        private RecipeService     $recipeService
    ) {}

    // -----------------------------------------------------------------------
    //  Page view
    // -----------------------------------------------------------------------

    public function index(int $recipeId): void
    {
        $recipe = $this->recipeService->getById($recipeId);
        $steps  = $this->stepService->getAll($recipeId);

        $this->view('recipes/steps', [
            'recipe'   => $recipe,
            'steps'    => $steps,
            'recipeId' => $recipeId,
        ]);
    }

    // -----------------------------------------------------------------------
    //  AJAX — Steps CRUD
    // -----------------------------------------------------------------------

    public function ajaxGetAll(int $recipeId)
    {
        $steps = $this->stepService->getAll($recipeId);
        return $this->json(['success' => true, 'data' => $steps]);
    }

    public function ajaxInsert(int $recipeId)
    {
        $request = Request::createFromGlobals();
        $data    = $this->getJson($request);

        $resp = $this->stepService->insert($recipeId, $data);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    public function ajaxUpdate(int $recipeId, int $id)
    {
        $request = Request::createFromGlobals();
        $data    = $this->getJson($request);

        $resp = $this->stepService->update($recipeId, $id, $data);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    public function ajaxDelete(int $recipeId, int $id)
    {
        $resp = $this->stepService->delete($recipeId, $id);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    // -----------------------------------------------------------------------
    //  AJAX — Reorder
    // -----------------------------------------------------------------------

    public function ajaxReorder(int $recipeId)
    {
        $request = Request::createFromGlobals();
        $data    = $this->getJson($request);

        $resp = $this->stepService->reorder($recipeId, $data['items'] ?? []);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    // -----------------------------------------------------------------------
    //  AJAX — Photos
    // -----------------------------------------------------------------------

    public function ajaxUploadPhoto(int $recipeId, int $stepId, int $slot)
    {
        $file = $_FILES['photo'] ?? null;
        if (!$file) {
            return $this->json(['success' => false, 'message' => 'No file uploaded'], 400);
        }

        $resp = $this->stepService->uploadPhoto($recipeId, $stepId, $slot, $file);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    public function ajaxDeletePhoto(int $recipeId, int $stepId, int $slot)
    {
        $resp = $this->stepService->deletePhoto($recipeId, $stepId, $slot);
        return $this->json($resp, $resp['code'] ?? 200);
    }
}
