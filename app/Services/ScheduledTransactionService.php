<?php
namespace App\Services;

use App\Models\ScheduledTransaction;
use App\Repositories\SheduledTransactionRepo;
use Auth;
use Carbon\Carbon;

class ScheduledTransactionService
{
    protected $sheduledTransactionRepo;


    public function __construct(SheduledTransactionRepo $sheduledTransactionRepo)
    {
        $this->sheduledTransactionRepo = $sheduledTransactionRepo;
    }



    // RÉCUPÉRER UNE TRANSACTION PROGRAMMÉE PAR SON ID:
    public function findById($transactionId)
    {
        return ScheduledTransaction::find($transactionId);
    }


    // AJOUT DE PLANIFICATION DE TRANSFERT
    public function addScheduledTransaction(array $data)
    {   
        // dd($data); // Ajout pour débogage

        $client = Auth::user();

        if ($client->telephone === $data['numero_destinataire']) {
            throw new \Exception('Vous ne pouvez pas planifier un dépôt pour vous même');
        }


        $requiredKeys = ['date', 'numero_destinataire', 'montant'];
        foreach ($requiredKeys as $key) {
            if (!isset($data[$key])) {
                throw new \Exception("La clé '$key' est requise.");
            }
        }


        // Déterminer la prochaine date d'exécution (initialement égale à la date prévue)
        $prochaineExecution = Carbon::parse($data['date']);

        // Créer et sauvegarder la transaction programmée
        return ScheduledTransaction::create([
            'client_id' => $client->id,
            'numero_destinataire' => $data['numero_destinataire'],
            'montant' => $data['montant'],
            'date' => $data['date'],
            'frequence' => $data['frequence'] ?? 'monthly', // Valeur par défaut : Mensuel
            'prochaine_execution' => $prochaineExecution->toDateString(),
        ]);
    }

    // RÉCUPÉRER LES TRANSACTIONS PLANIFIÉES DE L'UTILISATEUR CONNECTÉ:
    public function getClientSheduledTransactions($clientId)
    {
        return $this->sheduledTransactionRepo->getSheduledTransactionByClientId($clientId);
    }

    public function cancelShedulTransactiion($shedulTransactionId)
    {
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