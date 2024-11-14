<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MultipleTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'phone_numbers' => 'required|array|min:1',
            'phone_numbers.*' => [
                'required',
                'regex:/^(\+221\s?)?((77|76|75|70|78)\s?\d{3}\s?\d{2}\s?\d{2}|33\s?8\s?\d{3}\s?\d{3})$/',
                'different:client.telephone'
            ],
            'amount' => 'required|numeric|min:1',
        ];
    }

    public function messages()
    {
        return [

            'phone_numbers.requires' => 'Vous devez choisir au moins un numéro'
        ];
    }
}