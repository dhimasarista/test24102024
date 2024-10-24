<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateExpenseRequest extends FormRequest
{
    public function rules()
    {
        return [
            'amount' => 'required|integer|min:1',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
