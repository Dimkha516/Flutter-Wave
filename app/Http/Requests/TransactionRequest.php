<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'type' => 'required|in:envoi,retrait,paiement,depot',
            'montant' => 'required|numeric|min:1',
            'etat' => 'in:encours,effectue,annule',
        ];

        // Règles spécifiques selon le type de transaction

        if ($this->input('type') === 'envoi') {
            $rules['numero_destinataire'] = [
                'required',
                'regex:/^(\+221\s?)?((77|76|75|70|78)\s?\d{3}\s?\d{2}\s?\d{2}|33\s?8\s?\d{3}\s?\d{3})$/',
                'different:client.telephone'
            ];
        }

        if (in_array($this->input('type'), ['retrait', 'depot'])) {
            $rules['distributeur_id'] = 'required|exists:distributeurs,id';
        }

        if ($this->input('type') === 'paiement') {
            $rules['service_id'] = 'required|exists:services,id';
        }
        return $rules;
    }
}