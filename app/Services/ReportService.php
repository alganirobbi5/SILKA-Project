<?php

namespace App\Services;

use App\Models\Kategori;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Satu sumber dataset untuk web, print, dan export (PRD RPT-002/RPT-005).
     */
    public function query(array $filters = [])
    {
        $query = Transaksi::with(['kategori', 'coa'])
            ->filterKategori($filters['kategori_id'] ?? null)
            ->filterJenis($filters['jenis'] ?? null)
            ->filterTanggal($filters['tanggal_awal'] ?? null, $filters['tanggal_akhir'] ?? null);

        return $query->orderBy('tanggal', 'asc')->orderBy('id', 'asc');
    }

    public function data(array $filters = [])
    {
        return $this->query($filters)->get();
    }

    public function totals(array $filters = [])
    {
        $query = DB::table('transaksi')
            ->when(($filters['kategori_id'] ?? null), function ($q) use ($filters) {
                return $q->where('kategori_id', $filters['kategori_id']);
            })
            ->when(($filters['jenis'] ?? null), function ($q) use ($filters) {
                return $q->where('jenis', $filters['jenis']);
            })
            ->when(($filters['tanggal_awal'] ?? null), function ($q) use ($filters) {
                return $q->where('tanggal', '>=', $filters['tanggal_awal']);
            })
            ->when(($filters['tanggal_akhir'] ?? null), function ($q) use ($filters) {
                return $q->where('tanggal', '<=', $filters['tanggal_akhir']);
            })
            ->selectRaw("SUM(CASE WHEN jenis = 'pemasukan' THEN nominal ELSE 0 END) AS pemasukan")
            ->selectRaw("SUM(CASE WHEN jenis = 'pengeluaran' THEN nominal ELSE 0 END) AS pengeluaran");

        $totals = $query->first();

        return [
            'pemasukan' => (float) ($totals->pemasukan ?? 0),
            'pengeluaran' => (float) ($totals->pengeluaran ?? 0),
            'selisih' => (float) ($totals->pemasukan ?? 0) - (float) ($totals->pengeluaran ?? 0),
        ];
    }

    public function kategoris()
    {
        return Kategori::orderBy('kategori')->get();
    }
}
