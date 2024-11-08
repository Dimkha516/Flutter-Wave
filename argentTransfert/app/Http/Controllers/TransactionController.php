<?php
namespace App\Http\Controllers;

use App\Http\Requests\MultipleTransactionRequest;
use App\Http\Requests\TransactionRequest;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Request;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function sendMoney(TransactionRequest $request): JsonResponse
    {
        try {
            $transaction = $this->transactionService->sendMoney($request->validated());
            return response()->json(['transaction' => $transaction, 'message' => 'Transaction effectuée avec succès'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }


    public function index()
    {
        // Obtenir l'utilisateur actuellement authentifié
        $client = Auth::user();

        // Récupérer les transactions du client
        $transactions = $this->transactionService->getClientTransactions($client->id);

        return response()->json($transactions);
    }

    public function sendMultiple(MultipleTransactionRequest $request)
    {
        $phoneNumbers = $request->input('phone_numbers');
        $amount = $request->input('amount');

        // Appeler le service pour traiter l'envoi multiple
        $result = $this->transactionService->sendMultiple($phoneNumbers, $amount);

        return response()->json($result);
    }

    public function cancel(Request $request, $transactionId)
    {
        $result = $this->transactionService->cancelTransaction($transactionId);
        // Vérifiez si l’annulation a échoué en raison de restrictions de temps ou de permissions
        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 403);
        }

        return response()->json($result); 
    }
}  