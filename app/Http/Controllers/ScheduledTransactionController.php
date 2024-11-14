<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduledTransactionRequest;
use App\Services\ScheduledTransactionService;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduledTransactionController extends Controller
{
    protected $scheduledTransactionService;

    public function __construct(ScheduledTransactionService $scheduledTransactionService)
    {
        $this->scheduledTransactionService = $scheduledTransactionService;
    }

    public function index()
    {
        // Obtenir l'utilisateur actuellement authentifié
        $client = Auth::user();

        $sheduledTransactions = $this->scheduledTransactionService->getClientSheduledTransactions($client->id);
        
        return response()->json($sheduledTransactions);
    }

    public function store(ScheduledTransactionRequest $request): JsonResponse
    {
        $scheduledTransaction = $this->scheduledTransactionService->addScheduledTransaction($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Transaction programmée ajoutée avec succès.',
            'data' => $scheduledTransaction
        ], 201);
    }

    public function cancel($shedulTransactionId)
    {
        $result = $this->scheduledTransactionService->cancelShedulTransactiion($shedulTransactionId);

        // Vérifiez si l’annulation a échoué en raison de restrictions de temps ou de permissions
        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 403);
        }

        return response()->json($result);
    }
}
