<?php
namespace App\Http\Controllers;

use App\Http\Requests\RegisterClientRequest;
use App\Services\ClientService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;


class ClientController extends Controller
{
    protected $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }


    public function index(): JsonResponse
    {
        $result = $this->clientService->getAllClients();

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }

    public function register(RegisterClientRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $client = $this->clientService->registerClient($validatedData);

            // Envoyer la carte par email après inscription
            $this->clientService->sendClientCard($client);

            return response()->json(['data' => $client, 'message' => 'Client inscrit avec succès'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->clientService->authenticateClient($request->validated());
        return response()->json(['token' => $token, 'message' => 'Connexion réussie'], 200);
    }
}