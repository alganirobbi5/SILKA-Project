<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTargetCapaianRequest extends FormRequest
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
                Rule::unique('target_capaians', 'tahun')->ignore($this->route('target_capaian')),
            ],
            'target_capaian' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages()
    {
        return (new StoreTargetCapaianRequest())->messages();
    }
}
