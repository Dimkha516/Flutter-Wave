<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ScheduledTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


// class ProcessScheduledTransactions implements ShouldQueue
class ProcessScheduledTransactions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Récupérer les transactions dont la prochaine exécution est aujourd'hui ou avant aujourd'hui
        $transactions = ScheduledTransaction::where('prochaine_execution', '<=', Carbon::today()->toDateString())->get();


        foreach ($transactions as $transaction) {
            DB::beginTransaction();

            try {
                $client = $transaction->client;

                // Vérifier si le solde du client est suffisant pour chaque transaction
                if ($client->solde >= $transaction->montant) {
                    // Mettre à jour le solde du client
                    $client->solde -= $transaction->montant;
                    $client->save();

                    // Enregistrer la transaction
                    DB::table('transactions')->insert([
                        'client_id' => $client->id,
                        'numero_destinataire' => $transaction->numero_destinataire,
                        'type' => 'envoi',
                        'montant' => $transaction->montant,
                        'etat' => 'effectue',
                        'date' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    Log::info("Transaction envoyée à {$transaction->numero_destinataire} pour un montant de {$transaction->montant}.");
                } else {
                    Log::warning("Solde insuffisant pour la transaction programmée du client {$client->id}.");
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Erreur lors du traitement de la transaction programmée : " . $e->getMessage());
            }

            // Mettre à jour la prochaine date d'exécution
            $transaction->updateNextExecutionDate();
        }
    }
}
