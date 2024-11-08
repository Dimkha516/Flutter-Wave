<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

// class Client extends Model
class Client extends Authenticatable
{
    // use HasFactory;
    use HasApiTokens, Notifiable;
    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'email',
        'mot_de_passe',
        'solde',
        'qr_code'
    ];

     // Masquer le mot de passe lors des réponses JSON
     protected $hidden = ['mot_de_passe'];

    // Relation avec les contacts (un client a plusieurs contacts)
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    // Relation avec les transactions (un client peut avoir plusieurs transactions)
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
