<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKategoriRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'kategori' => [
                'required',
                'string',
                'max:150',
                Rule::unique('kategori', 'kategori')->ignore($this->route('kategori')),
            ],
        ];
    }

    public function messages()
    {
        return (new StoreKategoriRequest())->messages();
    }
}
