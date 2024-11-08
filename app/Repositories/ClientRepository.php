<?php
namespace App\Repositories;

use App\Models\Client;

class ClientRepository
{
    protected $model;

    public function __construct(Client $client)
    {
        $this->model = $client;
    }

    // Méthode pour obtenir un client par ID
    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function findByPhone($telephone)
    {
        return $this->model->where('telephone', $telephone)->first();
    }

}