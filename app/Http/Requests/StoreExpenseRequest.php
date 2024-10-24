<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function rules()
    {
        return [
            'amount' => 'required|integer|min:1',
        ];
    }
}
