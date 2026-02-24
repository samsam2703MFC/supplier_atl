<?php
namespace App\Supplier\app\Services\Client;


use App\Supplier\app\Repositories\Client\ClientRepository;

class ClientService
{
    public function __construct(
        private ClientRepository $clientRepository
    )
    {
    }

    public function getAll()
    {
        return $this->clientRepository->getAll();
    }

    public function getById($id)
    {
        return $this->clientRepository->getById($id);
    }
}