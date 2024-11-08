<?php
namespace App\Services;

use App\Repositories\ClientRepository;
use App\Repositories\TransactionRepository;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

class TransactionService
{
    protected $transactionRepository;
    protected $clientRepository;

    public function __construct(TransactionRepository $transactionRepository, ClientRepository $clientRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->clientRepository = $clientRepository;
    }


    //-----------------------ENDPOINT POUR EFFECTUER UNE TRANSACTION:
    public function sendMoney(array $data)
    {
        $client = auth()->user();
        // $destinataire = $this->clientRepository->findByPhone($data['numero_destinataire']);
        $destinataire = $data['numero_destinataire'];

        if (!$destinataire) {
            throw new \Exception("Saisir un numéro de destinataire valide !");
        }

        if ($client->telephone === $data['numero_destinataire']) {
            throw new \Exception('Vous ne pouvez pas effectuer un dépôt pour vous même');
        }

        if ($data['montant'] > $client->solde) {
            throw new \Exception('Le montant à envoyer ne peut pas dépasser votre solde.');
        }

        // Déduction du solde
        $client->solde -= $data['montant'];
        $client->save();


        // Création de la transaction
        return $this->transactionRepository->create([
            'client_id' => $client->id,
            'numero_destinataire' => $data['numero_destinataire'],
            'type' => 'envoi',
            'montant' => $data['montant'],
            'etat' => 'effectue',
        ]);
    }


    //-----------------------ENDPOINT POUR LISTER LES TRANSACTIONS DE L'UTILISATEUR
    public function getClientTransactions($clientId)
    {
        return $this->transactionRepository->getTransactionsByClientId($clientId);
    }



    //-----------------------ENDPOINT POUR EFFECTUER DES TRANSACTIONS MUTIPLES
    public function sendMultiple(array $phoneNumbers, float $amount)
    {
        $client = Auth::user();
        $totalAmountToSend = count($phoneNumbers) * $amount;
        $remainingBalance = $client->solde;


        // Initialiser les listes pour les numéros envoyés et non envoyés
        $successfulTransfers = [];
        $failedTransfers = [];

        DB::beginTransaction();

        try {
            foreach ($phoneNumbers as $phoneNumber) {
                if ($phoneNumber === $client->telephone) {
                    throw new \Exception('Vous ne pouvez pas effectuer un dépôt pour vous même');
                }
                if ($remainingBalance >= $amount) {
                    // Effectuer la transaction si le solde est suffisant
                    $this->transactionRepository->create([
                        'client_id' => $client->id,
                        'numero_destinataire' => $phoneNumber,
                        'type' => 'envoi',
                        'montant' => $amount,
                        'etat' => 'effectue',
                    ]);

                    // Mettre à jour le solde et ajouter le numéro à la liste des transferts réussis
                    $remainingBalance -= $amount;
                    $successfulTransfers[] = $phoneNumber;
                } else {
                    // Si le solde est insuffisant, ajouter le numéro aux échecs
                    $failedTransfers[] = $phoneNumber;
                }
            }
            // Mettre à jour le solde du client
            $client->solde = $remainingBalance;
            $client->save();

            DB::commit();

            return [
                'total_sent' => count($successfulTransfers) * $amount,
                'successful_transfers' => $successfulTransfers,
                'failed_transfers' => $failedTransfers,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    //-----------------------ENDPOINT POUR ANNULER UNE TRANSACTION
    public function cancelTransaction($transactionId)
    {
        $client = Auth::user();
        $transaction = $this->transactionRepository->findById($transactionId);

        // Vérifier si la transaction appartient bien au client
        if (!$transaction || $transaction->client_id !== $client->id) {
            return ['error' => 'Transaction introuvable ou non autorisée.'];
        }

        // Vérifier si la transaction a été effectuée il y a moins de 30 minutes
        $timeDifference = Carbon::now()->diffInMinutes($transaction->date);
        if ($timeDifference > 30) {
            return ['error' => 'L\'annulation n\'est plus possible au-delà de 30 minutes après le transfert.'];
        }

        // Annuler la transaction et rembourser le montant
        DB::beginTransaction();
        try {
            $transaction->etat = 'annule';
            $transaction->save();

            // Mettre à jour le solde du client
            $client->solde += $transaction->montant;
            $client->save();

            DB::commit();

            return ['success' => true, 'message' => 'Transaction annulée avec succès.'];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}