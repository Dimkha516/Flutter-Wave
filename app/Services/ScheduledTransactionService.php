<?php
namespace App\Services;

use App\Models\ScheduledTransaction;
use Auth;

class ScheduledTransactionService
{   
    public function findById($transactionId)
    {
        return ScheduledTransaction::find($transactionId);
    }

    public function addScheduledTransaction(array $data)
    {
        $client = Auth::user();

        if ($client->telephone === $data['numero_destinataire']) {
            throw new \Exception('Vous ne pouvez pas planifier un dépôt pour vous même');   
        }

        // Créer et sauvegarder la transaction programmée
        return ScheduledTransaction::create([
            'client_id' => $client->id,
            'numero_destinataire' => $data['numero_destinataire'],
            'montant' => $data['montant']
        ]);
    }

    public function cancelShedulTransactiion($shedulTransactionId){
        $client = Auth::user();
       
        $shedulTransaction = $this->findById($shedulTransactionId);
        // Vérifier si la transaction appartient bien au client
       
        if (!$shedulTransaction || $shedulTransaction->client_id !== $client->id) {
            return ['error' => 'Transaction introuvable ou non autorisée.'];
        }

        $shedulTransaction->delete();

        return ['success' => 'Transaction annulée avec succès.'];
    }
}