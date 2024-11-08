<?php

namespace App\Jobs;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Storage;


class GenerateQrCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    protected $client;
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Générer le QR code à partir du numéro de téléphone
        $qrCode = QrCode::format('png')->size(300)->generate($this->client->telephone);

        // Sauvegarder la carte avec le QR code
        $fileName = 'cards/' . $this->client->id . '_card.png';
        Storage::put($fileName, $qrCode);

        // Mettre à jour le chemin de la carte pour le client:
        $this->client->update(['qr_code' => $fileName]);
    }
}
