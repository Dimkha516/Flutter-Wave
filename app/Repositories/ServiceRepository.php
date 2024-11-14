<?php
namespace App\Repositories;

use App\Models\Service;

class ServiceRepository{
    protected $model;

    public function __construct(Service $service){
        $this->service = $service;
    }
    
    public function create(array $data){
        return $this->model->create($data);
    }

    public function findById($serviceId){
        return Service::find($serviceId);
    }


}