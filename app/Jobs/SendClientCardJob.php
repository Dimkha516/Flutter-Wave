<?php

namespace App\Jobs;

use App\Mail\ClientCardMail;
use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendClientCardJob implements ShouldQueue
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
        // Envoyer l'email avec la carte:
        try {
            Mail::to($this->client->email)->send(new ClientCardMail($this->client));
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
        }
    }
}
