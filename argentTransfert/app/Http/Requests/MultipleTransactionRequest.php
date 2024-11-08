<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class   MultipleTransactionRequest extends FormRequest{
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
                'regex:/^((77|76|75|70|78)\d{7})|(33[8]\d{6})$/',
                'different:client.telephone'
            ],
            'amount' => 'required|numeric|min:1',
        ];
    }
}