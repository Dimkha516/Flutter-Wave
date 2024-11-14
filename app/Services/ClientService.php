<?php
namespace App\Services;

use App\Jobs\GenerateQrCodeJob;
use App\Jobs\SendClientCardJob;
use App\Models\Client;
use App\Repositories\ClientRepository;
use Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Validation\ValidationException;
// use BaconQrCode\Encoder\QrCode;
// use SimpleSoftwareIO\QrCode;

use Hash;
use Storage;

class ClientService
{
    protected $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function getAllClients()
    {
        $clients = Client::all();

        if ($clients->isEmpty()) {
            return ['success' => false, 'message' => 'Aucun client existe dans la base de données', 'status' => 404];
        }

        return ['success' => true, 'message' => 'Liste des clients', 'data' => $clients, 'status' => 200];
    }

    public function registerClient(array $data)
    {
        $data['mot_de_passe'] = Hash::make($data['mot_de_passe']);
        $data['solde'] = 0;


        // Générer le QR code et enregistrer l'image dans le stockage local

        $qrCode = QrCode::format('png')->size(300)->generate($data['telephone']);
        $filePath = 'qr_codes/' . uniqid() . '.png';
        Storage::disk('public')->put($filePath, $qrCode);

        $data['qr_code'] = ($filePath);


        // Créer le client:
        $client = $this->clientRepository->create($data);

        // Déclencher le job pour générer la carte avec QR code: 
        GenerateQrCodeJob::dispatch($client);
        $this->sendClientCard($client);

        return $client;
    }

    public function sendClientCard($client)
    {
        // Déclencher le job pour envoyer la carte par email:
        SendClientCardJob::dispatch($client);

    }

    public function authenticateClient($data)
    {
        $client = $this->clientRepository->findByPhone($data['telephone']);

        if (!$client || !Hash::check($data['mot_de_passe'], $client->mot_de_passe)) {
            throw ValidationException::withMessages([
                'message' => ['Les informations d’identification sont incorrectes.'],
            ]);
        }

        // Générer un token Passport pour le client
        return $client->createToken('Client Access Token')->accessToken;
    }

    public function logout()
    {

        $client = Auth::user();
        $client->tokens->each(function ($token) {
            $token->delete();
        });

        return ['success' => true, 'message' => 'Déconnexion réussie', 'status' => 200];
    }

}