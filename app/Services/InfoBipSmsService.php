<?php
namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Infobip\Api\SmsApi;
use Infobip\Configuration;
use App\Services\SmsServiceInterface;
use Infobip\Model\SmsAdvancedTextualRequest;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;



class InfoBipSmsService implements SmsServiceInterface
{

    protected $smsApi;
    protected $client;
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        // // Initialisation de l'API InfoBip avec la clé et l'URL de base
        // $this->smsApi = new SmsApi(
        //     new Configuration(
        //         env('INFOBIP_API_KEY'),  // Clé API InfoBip
        //         env('INFOBIP_BASE_URL')  // URL de base InfoBip
        //     )
        // );

        $this->client = new Client();
        $this->baseUrl = 'https://api.infobip.com/sms/2/text/advanced';
        $this->apiKey = config('services.infobip.api_key');
    }

    public function sendSms(string $to, string $message)
    {
        try {
            // Création d'une destination avec le numéro du destinataire
            // $destination = new SmsDestination($to); // Directement une chaîne, non un tableau


            // Préparer les données de la requête
            $response = $this->client->post($this->baseUrl, [
                'headers' => [
                    'Authorization' => 'App ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'messages' => [
                        [
                            'from' => 'InfoSMS', // Assurez-vous que ce champ est défini correctement
                            'destinations' => [
                                [
                                    'to' => $to,
                                ],
                            ],
                            'text' => $message,
                        ],
                    ],
                ],
            ]);

            // return $response;
            return $response->getBody()->getContents();

        } catch (\Exception $e) {
            // Gestion des erreurs avec un log
            // \Log::error("Erreur InfoBip: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw new \Exception("Erreur lors de l'envoi du SMS via InfoBip : " . $e->getMessage());
        }
    }
}


