<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distributeur extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'mot_de_passe',
        'solde',
        'agence'
    ];

    // Relation avec les transactions (un distributeur peut avoir plusieurs transactions)
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Relation avec les opérations (un distributeur peut effectuer plusieurs opérations)
    public function operations()
    {
        return $this->hasMany(Operation::class);
    }
}
