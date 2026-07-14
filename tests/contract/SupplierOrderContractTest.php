<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use App\Supplier\app\Repositories\Me\SupplierOrderRepository;
use App\Supplier\core\Http\ApiClient;

final class CapturingApiClient extends ApiClient
{
    public array $calls = [];

    public function __construct()
    {
    }

    public function get($endpoint, $decode_json = true)
    {
        $this->calls[] = ['method' => 'GET', 'endpoint' => $endpoint, 'payload' => null];
        return ['success' => true, 'data' => []];
    }

    public function post($endpoint, $data)
    {
        $this->calls[] = ['method' => 'POST', 'endpoint' => $endpoint, 'payload' => $data];
        return ['success' => true, 'data' => []];
    }

    public function put($endpoint, $data)
    {
        $this->calls[] = ['method' => 'PUT', 'endpoint' => $endpoint, 'payload' => $data];
        return ['success' => true, 'data' => []];
    }

    public function patch($endpoint, $data)
    {
        $this->calls[] = ['method' => 'PATCH', 'endpoint' => $endpoint, 'payload' => $data];
        return ['success' => true, 'data' => []];
    }

    public function delete($endpoint)
    {
        $this->calls[] = ['method' => 'DELETE', 'endpoint' => $endpoint, 'payload' => null];
        return ['success' => true, 'data' => []];
    }
}

function assertSameContract(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        fwrite(STDERR, $message . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual:   ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

$client = new CapturingApiClient();
$repository = new SupplierOrderRepository($client);

$repository->getDetails(7, 11);
$repository->updateFinalItems(7, 11, [
    'version' => 3,
    'items' => [[
        'source_order_item_id' => 123,
        'catalog_product_id' => null,
        'final_pack_quantity' => 8,
        'reason' => 'shortage',
    ], [
        'source_order_item_id' => null,
        'catalog_product_id' => 456,
        'final_pack_quantity' => 2,
        'reason' => 'replacement',
    ]],
]);
$repository->updateTransport(7, 11, [
    'version' => 3,
    'mode' => 'OWN',
    'transport_type' => 'ROAD',
    'carrier_id' => null,
    'planned_delivery_date' => '2026-07-20',
    'loading_place' => 'Poznan',
    'loading_country' => 'PL',
    'delivery_place' => 'Paris',
    'delivery_country' => 'FR',
]);
$repository->updateTransport(7, 11, [
    'version' => 3,
    'mode' => 'EXTERNAL',
    'transport_type' => 'ROAD',
    'carrier_id' => 12,
    'planned_delivery_date' => '2026-07-20',
    'loading_place' => 'Poznan',
    'loading_country' => 'PL',
    'delivery_place' => 'Paris',
    'delivery_country' => 'FR',
]);
$repository->updateCarrier(7, 12, ['name' => 'Carrier']);
$repository->deleteCarrier(7, 12);
$repository->finalizationCheck(7, 11, ['version' => 3]);
$repository->generateDifferencesDocument(7, 11);

assertSameContract(['method' => 'GET', 'endpoint' => '/material-suppliers/7/orders/11/read-model', 'payload' => null], $client->calls[0], 'read-model endpoint mismatch');
assertSameContract('PUT', $client->calls[1]['method'], 'final-items method mismatch');
assertSameContract('/material-suppliers/7/orders/11/final-items', $client->calls[1]['endpoint'], 'final-items endpoint mismatch');
assertSameContract(['version', 'items'], array_keys($client->calls[1]['payload']), 'final-items top-level payload keys mismatch');
assertSameContract(['source_order_item_id', 'catalog_product_id', 'final_pack_quantity', 'reason'], array_keys($client->calls[1]['payload']['items'][0]), 'final-items item payload keys mismatch');
assertSameContract('OWN', $client->calls[2]['payload']['mode'], 'OWN transport mode mismatch');
assertSameContract('ROAD', $client->calls[2]['payload']['transport_type'], 'OWN transport type mismatch');
assertSameContract('EXTERNAL', $client->calls[3]['payload']['mode'], 'EXTERNAL transport mode mismatch');
assertSameContract(12, $client->calls[3]['payload']['carrier_id'], 'EXTERNAL carrier mismatch');
assertSameContract('PATCH', $client->calls[4]['method'], 'carrier update method mismatch');
assertSameContract('/material-suppliers/7/carriers/12', $client->calls[4]['endpoint'], 'carrier update endpoint mismatch');
assertSameContract('DELETE', $client->calls[5]['method'], 'carrier delete method mismatch');
assertSameContract('/material-suppliers/7/carriers/12', $client->calls[5]['endpoint'], 'carrier delete endpoint mismatch');
assertSameContract(['version' => 3], $client->calls[6]['payload'], 'finalization-check payload mismatch');
assertSameContract('/material-suppliers/7/orders/11/documents/difference', $client->calls[7]['endpoint'], 'difference document endpoint mismatch');

$view = file_get_contents(__DIR__ . '/../../src/app/Views/orders/show.twig');
assertSameContract(false, str_contains($view, 'documents/differences'), 'view still references plural differences endpoint');
assertSameContract(false, str_contains($view, '/deactivate'), 'view still references carrier deactivate alias');
assertSameContract(false, str_contains($view, "method = carrier ? 'PUT'"), 'view still updates carriers with PUT');
assertSameContract(true, substr_count($view, 'window.location.reload()') >= 8, 'view does not refetch after expected mutations');

echo "Supplier order contract tests passed.\n";
