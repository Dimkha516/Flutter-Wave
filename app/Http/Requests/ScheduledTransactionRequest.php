<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduledTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'numero_destinataire' => [
                'required',
                'regex:/^((77|76|75|70|78)\d{3}\d{2}\d{2})|(33[8]\d{2}\d{2}\d{2})$/',
                'different:client.telephone'
            ],
            'montant' => 'required|numeric|min:0.01'
        ];
    }

    public function messages()
    {
        return [
            'numero_destinataire.required' => 'Le numéro du destinataire est obligatoire.',
            'numero_destinataire.regex' => 'Le format du numéro du destinataire est invalide.',
            'montant.required' => 'Le montant est obligatoire.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'montant.min' => 'Le montant doit être supérieur à zéro.'
        ];
    }
}
