<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApproverRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|unique:approvers,name|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama pemeriksa harus diisi.',
            'name.unique' => 'Nama pemeriksa harus unik.',
        ];
    }
}
