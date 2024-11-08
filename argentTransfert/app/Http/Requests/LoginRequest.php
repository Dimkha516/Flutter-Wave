<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'telephone' => 'required|string',
            'mot_de_passe' => 'required|numeric',
            // 'mot_de_passe' => 'required|string',
        ];
    }
}
