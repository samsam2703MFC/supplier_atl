<?php
namespace App\Supplier\app\Services\Me;

use App\Supplier\app\Repositories\Me\SupplierOrderRepository;
use App\Supplier\core\Support\GlobalRegistry;

class SupplierOrderService
{
    public function __construct(
        private SupplierOrderRepository $orderRepository
    ) {}

    public function getAll(array $filters = []): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->getAll($supplierId, $filters);
    }

    public function getById(int $orderId): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->getById($supplierId, $orderId);
    }

    public function getDetails(int $orderId): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->getDetails($supplierId, $orderId);
    }

    public function accept(int $orderId, array $payload): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->accept($supplierId, $orderId, $payload);
    }

    public function reject(int $orderId, array $payload): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->reject($supplierId, $orderId, $payload);
    }

    public function cancel(int $orderId, array $payload): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->cancel($supplierId, $orderId, $payload);
    }

    public function updateFinalItems(int $orderId, array $payload): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->updateFinalItems($supplierId, $orderId, $payload);
    }

    public function updateTransport(int $orderId, array $payload): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->updateTransport($supplierId, $orderId, $payload);
    }

    public function finalizationCheck(int $orderId): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->finalizationCheck($supplierId, $orderId);
    }

    public function finalize(int $orderId, array $payload): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->finalize($supplierId, $orderId, $payload);
    }

    public function getCarriers(): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->getCarriers($supplierId);
    }

    public function createCarrier(array $payload): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->createCarrier($supplierId, $payload);
    }

    public function updateCarrier(int $carrierId, array $payload): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->updateCarrier($supplierId, $carrierId, $payload);
    }

    public function deactivateCarrier(int $carrierId): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->deactivateCarrier($supplierId, $carrierId);
    }

    public function getDocuments(int $orderId): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->getDocuments($supplierId, $orderId);
    }

    public function getCmrStatus(int $orderId): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->getCmrStatus($supplierId, $orderId);
    }

    public function generateDifferencesDocument(int $orderId): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->generateDifferencesDocument($supplierId, $orderId);
    }

    public function generateReleaseDocument(int $orderId): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->generateReleaseDocument($supplierId, $orderId);
    }

    public function generateCmrDocument(int $orderId): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->generateCmrDocument($supplierId, $orderId);
    }

    public function getOrderDocument(int $orderId, int $documentId): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->getOrderDocument($supplierId, $orderId, $documentId);
    }

    public function getDocument(int $orderId): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->getDocument($supplierId, $orderId);
    }

    public function getBulkDocuments(array $orderIds): array
    {
        $supplierId = GlobalRegistry::get('user')['supplier_id'];
        return $this->orderRepository->getBulkDocuments($supplierId, $orderIds);
    }
}
