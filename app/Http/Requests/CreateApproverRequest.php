<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateApproverRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|unique:approvers,name',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
