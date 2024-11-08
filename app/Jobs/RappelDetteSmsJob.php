<?php

namespace App\Jobs;

use App\Services\InfoBipSmsService;
use App\Services\TwilioService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RappelDetteSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    public $nom;
    public $numero;

    public function __construct($nom, $numero)
    {   
        $this->nom = $nom;
        $this->numero = $numero;

        
    }

    

    

    /**
     * Execute the job.
     */

    public function handle(): void
    {


        $smsProvider = env('SMS_PROVIDER', 'infobip');  // Par défaut sur infobip
        $smsService = null;

        // Sélection du service de SMS en fonction du fournisseur
        if ($smsProvider === 'twilio') {
            $smsService = app(TwilioService::class);
        } elseif ($smsProvider === 'infobip') {
            $smsService = app(InfoBipSmsService::class);
        }
    }
}
