<?php
namespace App\Http\Controllers;

use App\Services\ServiceService;

class ServiceController
{
    protected $serviceService;

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    public function index()
    {
        $result = $this->serviceService->getAllServices();

        return response()->json(
            [
                'success' => $result['success'],
                'message' => $result['message'],
                'data' => $result['data'] ?? null,
            ],
            $result['status']
        );
    }

    public function show($serviceId)
    {
        $result = $this->serviceService->getServiceById($serviceId);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }
}
