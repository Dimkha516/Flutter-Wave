<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class RegisterClientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            // 'telephone' => 'required|string|unique:clients,telephone',
            'telephone' => [
                'required',
                'string',
                'unique:clients,telephone',
                'regex:/^(\+221\s?)?((77|76|75|70|78)\s?\d{3}\s?\d{2}\s?\d{2}|33\s?8\s?\d{3}\s?\d{3})$/',
            ],
            'email' => 'required|string|email|unique:clients,email',
            'mot_de_passe' => 'required|numeric|min:4',

        ];
    }   
}
