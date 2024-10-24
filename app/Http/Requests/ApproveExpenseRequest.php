<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveExpenseRequest extends FormRequest
{
    public function rules()
    {
        return [
            'approver_id' => 'required|exists:approvers,id',
        ];
    }
}
