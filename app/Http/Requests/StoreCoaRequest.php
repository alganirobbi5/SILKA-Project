<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCoaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'kode_coa' => ['required', 'string', 'max:255', Rule::unique('coa', 'kode_coa')],
            'nama_coa' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'string', 'max:255'],
            'cluster' => ['nullable', 'integer', 'exists:cluster,id_cluster'],
            'saldo_awal' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'kode_coa.required' => 'Kode COA wajib diisi.',
            'kode_coa.unique' => 'Kode COA sudah digunakan.',
            'nama_coa.required' => 'Nama COA wajib diisi.',
            'jenis.required' => 'Jenis wajib diisi.',
            'cluster.exists' => 'Cluster tidak valid.',
            'saldo_awal.numeric' => 'Saldo awal harus berupa angka.',
            'saldo_awal.min' => 'Saldo awal tidak boleh negatif.',
        ];
    }
}
