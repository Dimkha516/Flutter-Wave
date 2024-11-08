<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'nom',
        'prenom',
        'telephone'
    ];

    // Relation avec le client (chaque contact appartient à un client)
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
