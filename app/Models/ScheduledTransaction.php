<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'numero_destinataire',
        'montant',
        'date',
        'frequence',
        'prochaine_execution'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /*
    Mettre à jour la prochaine date d'échéance en fonction de la fréquence.
    */

    public function updateNextExecutionDate()
    {
        $nextExecution = Carbon::parse($this->prochaine_execution);
        switch ($this->frequence) {
            case 'daily':
                $nextExecution->addDay();
                break;
            case 'weekly':
                $nextExecution->addWeek();
                break;
            case 'monthly':
                $nextExecution->addMonth();
                break;
        }
        $this->prochaine_execution = $nextExecution->toDateString();
        $this->save();
    }

}
