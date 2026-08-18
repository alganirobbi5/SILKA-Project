<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransaksiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tanggal' => ['required', 'date'],
            'jenis' => ['required', Rule::in(['pemasukan', 'pengeluaran'])],
            'kategori_id' => ['required', 'integer', 'exists:kategori,id'],
            'coa_id' => ['required', 'integer', 'exists:coa,id'],
            'nominal' => ['required', 'numeric', 'min:0.01'],
            'keterangan' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages()
    {
        return (new StoreTransaksiRequest())->messages();
    }
}
