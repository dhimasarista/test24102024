<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveExpenseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'approver_id' => 'required|exists:approvers,id',
        ];
    }

    public function messages()
    {
        return [
            'approver_id.required' => 'ID pemeriksa harus diisi.',
            'approver_id.exists' => 'Pemeriksa yang dipilih tidak ditemukan.',
        ];
    }
}
