<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'distributeur_id',
        'numero_destinataire',
        'type',
        'montant',
        'etat',
        'service_id'
    ];


    // Relation avec le client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relation avec le distributeur
    public function distributeur()
    {
        return $this->belongsTo(Distributeur::class);
    }

    // Relation avec le service (si le type est "paiement")
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
