<?php
namespace App\Supplier\app\Repositories\RawMaterial;

use App\Supplier\core\Http\ApiClient;

/**
 * Lista jednostek miary z API (/units) — wymagana w formularzu surowca.
 */
class UnitRepository
{
    public function __construct(
        private ApiClient $apiClient
    ) {}

    public function getAll(): array
    {
        $response = $this->apiClient->get('/units');
        return $response['data'] ?? [];
    }
}

