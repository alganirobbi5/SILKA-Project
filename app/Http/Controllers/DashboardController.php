<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\TargetCapaian;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->query('year', date('Y'));
        if ($year < 2000 || $year > 2100) {
            $year = (int) date('Y');
        }

        $today = date('Y-m-d');
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');
        $startOfYear = $year . '-01-01';
        $endOfYear = $year . '-12-31';

        // Agregasi
        $hariIni = Transaksi::where('tanggal', $today)
            ->selectRaw("SUM(CASE WHEN jenis = 'pemasukan' THEN nominal ELSE 0 END) AS pemasukan")
            ->selectRaw("SUM(CASE WHEN jenis = 'pengeluaran' THEN nominal ELSE 0 END) AS pengeluaran")
            ->first();

        $bulanIni = Transaksi::whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->selectRaw("SUM(CASE WHEN jenis = 'pemasukan' THEN nominal ELSE 0 END) AS pemasukan")
            ->selectRaw("SUM(CASE WHEN jenis = 'pengeluaran' THEN nominal ELSE 0 END) AS pengeluaran")
            ->first();

        $tahunIni = Transaksi::whereBetween('tanggal', [$startOfYear, $endOfYear])
            ->selectRaw("SUM(CASE WHEN jenis = 'pemasukan' THEN nominal ELSE 0 END) AS pemasukan")
            ->selectRaw("SUM(CASE WHEN jenis = 'pengeluaran' THEN nominal ELSE 0 END) AS pengeluaran")
            ->first();

        $target = TargetCapaian::where('tahun', $year)->first();

        // Piutang & hutang tahun lalu (COA-CLASS-01 dari master COA existing)
        $prevYear = $year - 1;
        $prevYearStart = $prevYear . '-01-01';
        $prevYearEnd = $prevYear . '-12-31';

        $piutangCoaIds = Coa::where('jenis', 'Aset')
            ->where('nama_coa', 'like', '%Piutang%')
            ->pluck('id');
        $hutangCoaIds = Coa::where('jenis', 'Liabilitas')
            ->where('nama_coa', 'like', '%Utang%')
            ->pluck('id');

        $piutang = 0;
        if ($piutangCoaIds->isNotEmpty()) {
            $piutang = Transaksi::whereBetween('tanggal', [$prevYearStart, $prevYearEnd])
                ->whereIn('coa_id', $piutangCoaIds)
                ->selectRaw("SUM(CASE WHEN jenis = 'pemasukan' THEN nominal ELSE -nominal END) AS total")
                ->value('total') ?? 0;
        }

        $hutang = 0;
        if ($hutangCoaIds->isNotEmpty()) {
            $hutang = Transaksi::whereBetween('tanggal', [$prevYearStart, $prevYearEnd])
                ->whereIn('coa_id', $hutangCoaIds)
                ->selectRaw("SUM(CASE WHEN jenis = 'pemasukan' THEN nominal ELSE -nominal END) AS total")
                ->value('total') ?? 0;
        }

        $tahunTersedia = Transaksi::selectRaw('YEAR(tanggal) AS tahun')
            ->groupBy('tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->push((int) date('Y'))
            ->unique()
            ->sortDesc()
            ->values();

        return view('dashboard.index', compact(
            'year', 'hariIni', 'bulanIni', 'tahunIni', 'target',
            'piutang', 'hutang', 'tahunTersedia'
        ));
    }
}
