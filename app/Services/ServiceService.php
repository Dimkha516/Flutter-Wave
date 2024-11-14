<?php
namespace App\Services;

use App\Models\Service;
use App\Repositories\ServiceRepository;

class ServiceService{
    protected $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository){
        $this->serviceRepository = $serviceRepository;
    }

    public function getAllServices(){
        $services = Service::all();

        if ($services->isEmpty()) {
            return ['success' => false, 'message' => 'Aucun service existe dans la base de données', 'status' => 404];
        }

        return ['success' => true, 'message' => 'Liste des services', 'data' => $services, 'status' => 200];
    }

    public function getServiceById($serviceId){
        $service = Service::find($serviceId);

        if (!$service) {
            return ['success' => false, 'message' => 'Service non trouvé', 'status' => 404];
        }

        return ['success' => true, 'message' => 'Service recherché', 'data' => $service, 'status' => 200];
    }

}