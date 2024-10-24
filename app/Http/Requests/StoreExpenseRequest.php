<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize()
    {
        return true; // atau atur permission/authorization yang diperlukan
    }

    public function rules()
    {
        return [
            'amount' => 'required|numeric|min:0',
            'status_id' => 'required|exists:statuses,id',
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => 'Jumlah pengeluaran harus diisi.',
            'amount.numeric' => 'Jumlah pengeluaran harus berupa angka.',
            'status_id.required' => 'Status harus diisi.',
            'status_id.exists' => 'Status tidak valid.',
        ];
    }
}