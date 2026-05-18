<?php

namespace App\Supplier\app\Http\Controllers\Complaints;

use App\Supplier\app\Http\Controllers\Controller;
use App\Supplier\app\Services\Me\ComplaintService;
use App\Supplier\core\Support\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ComplaintsController extends Controller
{
    public function __construct(
        private ComplaintService $complaintService
    ) {}

    #[Route('GET', '/complaints')]
    public function index()
    {
        $filters = [];

        if (!empty($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }

        $data['complaints'] = $this->complaintService->getAll($filters);
        $data['open_complaints_count'] = $this->complaintService->getOpenCount();
        $data['current_path'] = '/complaints';
        $data['filters'] = $filters;

        $this->view("complaints/index", $data);
    }

    #[Route('GET', '/ajax/complaints/attachment/{uuid}')]
    public function getAttachmentPresignedUrl(string $uuid)
    {
        $result = $this->complaintService->getAttachmentPresignedUrl($uuid);
        return $this->json($result);
    }

    /**
     * PATCH /ajax/complaints/{id}
     * Umożliwia rozpatrzenie reklamacji portal-managed przez dostawcę.
     * Payload JSON: { status, production_response, accepted_qty, compensation_method }
     */
    #[Route('PATCH', '/ajax/complaints/{id}')]
    public function updateComplaint(string $id): JsonResponse
    {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];

        $result = $this->complaintService->update((int) $id, $body);

        if (!empty($result['success'])) {
            return $this->json([
                'ok'          => true,
                'description' => 'Reklamacja zaktualizowana',
            ]);
        }

        $errorDesc = $result['description'] ?? ($result['error'] ?? 'Błąd aktualizacji');
        return $this->json([
            'ok'          => false,
            'description' => is_string($errorDesc) ? $errorDesc : 'Błąd aktualizacji',
        ], 400);
    }
}
