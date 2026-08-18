<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCoaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'kode_coa' => [
                'required',
                'string',
                'max:255',
                Rule::unique('coa', 'kode_coa')->ignore($this->route('coa')),
            ],
            'nama_coa' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'string', 'max:255'],
            'cluster' => ['nullable', 'integer', 'exists:cluster,id_cluster'],
        ];
    }

    public function messages()
    {
        return (new StoreCoaRequest())->messages();
    }
}
