<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransaksiRequest extends FormRequest
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
        return [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Tanggal tidak valid.',
            'jenis.required' => 'Jenis wajib diisi.',
            'jenis.in' => 'Jenis harus pemasukan atau pengeluaran.',
            'kategori_id.required' => 'Kategori wajib diisi.',
            'kategori_id.exists' => 'Kategori tidak valid.',
            'coa_id.required' => 'Akun COA wajib diisi.',
            'coa_id.exists' => 'Akun COA tidak valid.',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
            'nominal.min' => 'Nominal harus lebih besar dari 0.',
            'keterangan.required' => 'Keterangan wajib diisi.',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter.',
        ];
    }
}
