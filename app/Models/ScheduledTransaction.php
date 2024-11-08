<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'numero_destinataire',
        'montant'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

}
