<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKategoriRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'kategori' => ['required', 'string', 'max:150', Rule::unique('kategori', 'kategori')],
        ];
    }

    public function messages()
    {
        return [
            'kategori.required' => 'Nama kategori wajib diisi.',
            'kategori.max' => 'Nama kategori maksimal 150 karakter.',
            'kategori.unique' => 'Nama kategori sudah digunakan.',
        ];
    }
}
