<?php

namespace App\Supplier\app\Http\Controllers\Me;


use App\Supplier\app\Http\Controllers\Controller;
use App\Supplier\app\Services\Me\SupplierService;
use App\Supplier\core\Support\Route;

class ProfileController extends Controller
{

    public function __construct(
        private SupplierService $supplierService
    ) {}


    #[Route('GET', '/me')]
    public function index()
    {
        $data['supplier'] = $this->supplierService->getMe();

        $this->view("me/about", $data);
    }

    #[Route('POST', '/me')]
    public function update()
    {
        $allowed = ['iban', 'website_url', 'notes'];
        $payload = array_intersect_key($_POST, array_flip($allowed));

        // Sanitise: strip tags from text fields
        foreach ($payload as $key => $value) {
            $payload[$key] = strip_tags(trim($value));
        }

        $result = $this->supplierService->updateProfile($payload);

        if ($result && ($result['success'] ?? false)) {
            $this->successes[] = 'profile_saved';
        } else {
            $this->errors[] = 'profile_save_failed';
        }

        $data['supplier'] = $this->supplierService->getMe();
        $this->view("me/about", $data);
    }

    #[Route('POST', '/me/logo')]
    public function uploadLogo()
    {
        $file = $_FILES['logo'] ?? null;

        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $this->errors[] = 'logo_upload_failed';
        } else {
            $result = $this->supplierService->uploadLogo($file);

            if ($result && ($result['success'] ?? false)) {
                $this->successes[] = 'logo_uploaded';
            } else {
                $this->errors[] = 'logo_upload_failed';
            }
        }

        $data['supplier'] = $this->supplierService->getMe();
        $this->view("me/about", $data);
    }
}
