<?php
namespace App\Supplier\app\Services\Me;

use App\Supplier\app\Repositories\Me\ComplaintRepository;
use App\Supplier\core\Support\GlobalRegistry;

class ComplaintService
{
    public function __construct(
        private ComplaintRepository $complaintRepository
    ) {}

    public function getAll(array $filters = []): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->complaintRepository->getAll($supplierId, $filters);
    }

    public function getOpenCount(): int
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        $open = $this->complaintRepository->getAll($supplierId, ['status' => 'NEW,IN_REVIEW']);
        return count($open);
    }

    /**
     * Aktualizuje reklamację z poziomu portalu dostawcy.
     * Deleguje do API (walidacja statusów i własności odbywa się po stronie API).
     */
    public function update(int $complaintId, array $data): array
    {
        $supplierId = (int) GlobalRegistry::get('user')['supplier_id'];
        return $this->complaintRepository->update($complaintId, $supplierId, $data);
    }

    public function getAttachmentPresignedUrl(string $uuid): array
    {
        return $this->complaintRepository->getAttachmentPresignedUrl($uuid);
    }
}
