<?php

namespace App\Supplier\app\Http\Controllers\Orders;

use App\Supplier\app\Http\Controllers\Controller;
use App\Supplier\app\Services\Me\SupplierOrderService;
use App\Supplier\core\Support\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class OrdersController extends Controller
{
    public function __construct(
        private SupplierOrderService $orderService
    ) {}

    #[Route('GET', '/orders')]
    public function index()
    {
        $filters = [];

        if (!empty($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }
        if (!empty($_GET['from'])) {
            $filters['from'] = $_GET['from'];
        }
        if (!empty($_GET['to'])) {
            $filters['to'] = $_GET['to'];
        }

        $data['orders'] = $this->orderService->getAll($filters);

        $data['current_path'] = '/orders';
        $data['filters'] = $filters;

        $this->view('orders/index', $data);
    }

    #[Route('GET', '/ajax/orders/{orderId:\d+}')]
    public function show(string $orderId): JsonResponse
    {
        $order = $this->orderService->getById((int) $orderId);

        if (empty($order)) {
            return $this->json(['description' => 'Order not found'], 404);
        }

        return $this->json($order);
    }

    #[Route('GET', '/orders/{orderId:\d+}')]
    public function details(string $orderId): void
    {
        $response = $this->orderService->getDetails((int) $orderId);
        $statusCode = (int) ($response['error'] ?? 200);

        $data = [
            'order' => $response['data'] ?? [],
            'current_path' => '/orders',
            'api_status' => $response['success'] ? 200 : $statusCode,
        ];

        if (empty($response['success'])) {
            http_response_code(in_array($statusCode, [403, 404], true) ? $statusCode : 502);
        }

        $this->view('orders/show', $data);
    }

    #[Route('POST', '/ajax/orders/{orderId:\d+}/accept')]
    public function accept(string $orderId): JsonResponse
    {
        $payload = $this->getJson(Request::createFromGlobals());

        return $this->proxyLifecycleResponse($this->orderService->accept((int) $orderId, $payload));
    }

    #[Route('POST', '/ajax/orders/{orderId:\d+}/reject')]
    public function reject(string $orderId): JsonResponse
    {
        $payload = $this->getJson(Request::createFromGlobals());

        return $this->proxyLifecycleResponse($this->orderService->reject((int) $orderId, $payload));
    }

    #[Route('POST', '/ajax/orders/{orderId:\d+}/cancel')]
    public function cancel(string $orderId): JsonResponse
    {
        $payload = $this->getJson(Request::createFromGlobals());

        return $this->proxyLifecycleResponse($this->orderService->cancel((int) $orderId, $payload));
    }

    #[Route('PUT', '/ajax/orders/{orderId:\d+}/final-items')]
    public function updateFinalItems(string $orderId): JsonResponse
    {
        $payload = $this->getJson(Request::createFromGlobals());

        return $this->proxyLifecycleResponse($this->orderService->updateFinalItems((int) $orderId, $payload));
    }

    #[Route('PUT', '/ajax/orders/{orderId:\d+}/transport')]
    public function updateTransport(string $orderId): JsonResponse
    {
        $payload = $this->getJson(Request::createFromGlobals());

        return $this->proxyLifecycleResponse($this->orderService->updateTransport((int) $orderId, $payload));
    }

    #[Route('POST', '/ajax/orders/{orderId:\d+}/finalization-check')]
    public function finalizationCheck(string $orderId): JsonResponse
    {
        $payload = $this->getJson(Request::createFromGlobals());

        return $this->proxyLifecycleResponse($this->orderService->finalizationCheck((int) $orderId, $payload));
    }

    #[Route('POST', '/ajax/orders/{orderId:\d+}/finalize')]
    public function finalize(string $orderId): JsonResponse
    {
        $payload = $this->getJson(Request::createFromGlobals());

        return $this->proxyLifecycleResponse($this->orderService->finalize((int) $orderId, $payload));
    }

    #[Route('GET', '/ajax/orders/carriers')]
    public function carriers(): JsonResponse
    {
        $response = $this->orderService->getCarriers();

        if (empty($response['success'])) {
            return $this->proxyLifecycleResponse($response);
        }

        return $this->json($response['data'] ?? []);
    }

    #[Route('POST', '/ajax/orders/carriers')]
    public function createCarrier(): JsonResponse
    {
        $payload = $this->getJson(Request::createFromGlobals());

        return $this->proxyLifecycleResponse($this->orderService->createCarrier($payload));
    }

    #[Route('PATCH', '/ajax/orders/carriers/{carrierId:\d+}')]
    public function updateCarrier(string $carrierId): JsonResponse
    {
        $payload = $this->getJson(Request::createFromGlobals());

        return $this->proxyLifecycleResponse($this->orderService->updateCarrier((int) $carrierId, $payload));
    }

    #[Route('DELETE', '/ajax/orders/carriers/{carrierId:\d+}')]
    public function deleteCarrier(string $carrierId): JsonResponse
    {
        return $this->proxyLifecycleResponse($this->orderService->deleteCarrier((int) $carrierId));
    }

    #[Route('GET', '/ajax/orders/{orderId:\d+}/documents')]
    public function documents(string $orderId): JsonResponse
    {
        $response = $this->orderService->getDocuments((int) $orderId);

        if (empty($response['success'])) {
            return $this->proxyLifecycleResponse($response);
        }

        return $this->json($response['data'] ?? []);
    }

    #[Route('GET', '/ajax/orders/{orderId:\d+}/documents/cmr/status')]
    public function cmrStatus(string $orderId): JsonResponse
    {
        $response = $this->orderService->getCmrStatus((int) $orderId);

        if (empty($response['success'])) {
            return $this->proxyLifecycleResponse($response);
        }

        return $this->json($response['data'] ?? []);
    }

    #[Route('POST', '/ajax/orders/{orderId:\d+}/documents/difference')]
    public function generateDifferencesDocument(string $orderId): JsonResponse
    {
        return $this->proxyLifecycleResponse($this->orderService->generateDifferencesDocument((int) $orderId));
    }

    #[Route('POST', '/ajax/orders/{orderId:\d+}/documents/release')]
    public function generateReleaseDocument(string $orderId): JsonResponse
    {
        return $this->proxyLifecycleResponse($this->orderService->generateReleaseDocument((int) $orderId));
    }

    #[Route('POST', '/ajax/orders/{orderId:\d+}/documents/cmr')]
    public function generateCmrDocument(string $orderId): JsonResponse
    {
        return $this->proxyLifecycleResponse($this->orderService->generateCmrDocument((int) $orderId));
    }

    #[Route('GET', '/orders/{orderId:\d+}/documents/{documentId:\d+}/download')]
    public function downloadOrderDocument(string $orderId, string $documentId): void
    {
        $this->streamPdfResponse($this->orderService->getOrderDocument((int) $orderId, (int) $documentId));
    }

    #[Route('GET', '/orders/{orderId:\d+}/document')]
    public function downloadDocument(string $orderId): void
    {
        $this->streamPdfResponse($this->orderService->getDocument((int) $orderId));
    }

    #[Route('POST', '/orders/documents/download')]
    public function downloadSelectedDocuments(): void
    {
        $orderIds = $_POST['order_ids'] ?? [];
        if (!is_array($orderIds) || empty($orderIds)) {
            redirect('orders');
        }

        $orderIds = array_values(array_filter(array_map('intval', $orderIds), fn($id) => $id > 0));
        if (empty($orderIds)) {
            redirect('orders');
        }

        $this->streamPdfResponse($this->orderService->getBulkDocuments($orderIds));
    }

    private function proxyLifecycleResponse(array $response): JsonResponse
    {
        $statusCode = (int) ($response['code'] ?? $response['error'] ?? 500);
        $payload = $response['data'] ?? [];

        if (!is_array($payload)) {
            $payload = [];
        }

        $payload['success'] = !empty($response['success']);
        $payload['message'] = $response['message'] ?? $payload['message'] ?? null;
        $payload['description'] = $response['description'] ?? $payload['description'] ?? null;

        return $this->json($payload, !empty($response['success']) ? 200 : $statusCode);
    }

    private function streamPdfResponse(array $response): void
    {
        if (empty($response['success'])) {
            http_response_code((int) ($response['error'] ?? 500));
            echo 'Unable to download PDF document.';
            return;
        }

        header('Content-Type: application/pdf');

        $filename = $response['filename'] ?? 'orders.pdf';
        if (!str_ends_with(strtolower($filename), '.pdf')) {
            $filename .= '.pdf';
        }

        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $response['data'] ?? '';
    }
}
