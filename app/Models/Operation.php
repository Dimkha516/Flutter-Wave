<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributeur_id', 'client_id', 'type', 'date'
    ];

    // Relation avec le distributeur
    public function distributeur()
    {
        return $this->belongsTo(Distributeur::class);
    }

    // Relation avec le client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
