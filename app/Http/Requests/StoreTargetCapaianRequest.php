<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTargetCapaianRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tahun' => [
                'required',
                'integer',
                'digits:4',
                'between:2000,2100',
                Rule::unique('target_capaians', 'tahun'),
            ],
            'target_capaian' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'tahun.required' => 'Tahun wajib diisi.',
            'tahun.digits' => 'Tahun harus 4 digit.',
            'tahun.between' => 'Tahun harus antara 2000 dan 2100.',
            'tahun.unique' => 'Target untuk tahun tersebut sudah ada.',
            'target_capaian.required' => 'Nominal target wajib diisi.',
            'target_capaian.numeric' => 'Nominal target harus berupa angka.',
            'target_capaian.min' => 'Nominal target tidak boleh negatif.',
        ];
    }
}
