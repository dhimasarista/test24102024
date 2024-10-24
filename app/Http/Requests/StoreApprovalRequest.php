<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApprovalRequest extends FormRequest
{
    public function authorize()
    {
        return true; // atau atur permission/authorization yang diperlukan
    }

    public function rules()
    {
        return [
            'expense_id' => 'required|exists:expenses,id',
            'approver_id' => 'required|exists:approvers,id',
            'status_id' => 'required|exists:statuses,id',
        ];
    }

    public function messages()
    {
        return [
            'expense_id.required' => 'ID pengeluaran harus diisi.',
            'expense_id.exists' => 'Pengeluaran tidak valid.',
            'approver_id.required' => 'Pemeriksa harus diisi.',
            'approver_id.exists' => 'Pemeriksa tidak valid.',
            'status_id.required' => 'Status harus diisi.',
            'status_id.exists' => 'Status tidak valid.',
        ];
    }
}