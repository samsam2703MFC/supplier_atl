<?php
namespace App\Supplier\app\Repositories\Me;

use App\Supplier\core\Http\ApiClient;

class ComplaintRepository
{
    public function __construct(
        private ApiClient $apiClient
    ) {}

    public function getAll(int $supplierId, array $params = []): array
    {
        $url = "/material-suppliers/{$supplierId}/complaints";
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        $response = $this->apiClient->get($url);
        return $response['data'] ?? [];
    }

    public function update(int $complaintId, int $supplierId, array $data): array
    {
        return $this->apiClient->patch(
            "/material-suppliers/{$supplierId}/complaints/{$complaintId}",
            $data
        );
    }

    public function getAttachmentPresignedUrl(string $uuid): array
    {
        return $this->apiClient->get("/attachments/{$uuid}/presigned-url");
    }
}
