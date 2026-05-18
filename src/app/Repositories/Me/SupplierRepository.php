<?php

namespace App\Supplier\app\Repositories\Me;


use App\Supplier\app\Models\Me\SupplierModel;
use App\Supplier\core\Http\ApiClient;

class SupplierRepository
{
    public function __construct(
        private ApiClient $apiClient
    )
    {
    }

    public function getMe($id)
    {
        $resp = $this->apiClient->get('/material-suppliers/' . $id);
        return new SupplierModel($resp['data']) ?? null;
    }

    public function updateProfile($id, array $data)
    {
        return $this->apiClient->patch('/material-suppliers/' . $id . '/profile', $data);
    }

    public function uploadLogo($id, array $file)
    {
        return $this->apiClient->postMultipart('/material-suppliers/' . $id . '/logo', [], ['logo' => $file]);
    }

    public function getLaborDefaults(int $id): array
    {
        $resp = $this->apiClient->get('/material-suppliers/' . $id . '/labor-defaults');
        return $resp['data']['data'] ?? [];
    }

    public function updateLaborDefaults(int $id, array $data): array
    {
        return $this->apiClient->patch('/material-suppliers/' . $id . '/labor-defaults', $data);
    }

}