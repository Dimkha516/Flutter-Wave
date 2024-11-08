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
                'regex:/^((77|76|75|70|78)\d{3}\d{2}\d{2})|(33[8]\d{2}\d{2}\d{2})$/',
            ],
            'email' => 'required|string|email|unique:clients,email',
            'mot_de_passe' => 'required|numeric|min:4',

        ];
    }   
}
