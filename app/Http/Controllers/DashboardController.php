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
            ->whereYear('tanggal', '>=', 2000)
            ->whereYear('tanggal', '<=', (int) date('Y'))
            ->groupBy('tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->push((int) date('Y'))
            ->unique()
            ->sortDesc()
            ->values();

        // Transaksi terbaru
        $recentTransaksis = Transaksi::with(['kategori', 'coa'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        // Capaian target tahun terpilih
        $tahunMasuk = (float) ($tahunIni->pemasukan ?? 0);
        $tahunKeluar = (float) ($tahunIni->pengeluaran ?? 0);
        $targetNominal = $target ? (float) $target->target_capaian : 0.0;

        $targetPersen = $targetNominal > 0 ? min(100, round($tahunMasuk / $targetNominal * 100, 1)) : null;

        $totalTahun = $tahunMasuk + $tahunKeluar;
        $persenPemasukan = $totalTahun > 0 ? round($tahunMasuk / $totalTahun * 100, 1) : 50;
        $persenPengeluaran = $totalTahun > 0 ? round($tahunKeluar / $totalTahun * 100, 1) : 50;

        // Status data tahun terpilih
        $selectedHasData = Transaksi::whereYear('tanggal', $year)->exists();
        $latestDataYear = Transaksi::whereYear('tanggal', '>=', 2000)
            ->whereYear('tanggal', '<=', (int) date('Y'))
            ->max('tanggal');
        $latestDataYear = $latestDataYear ? (int) substr($latestDataYear, 0, 4) : null;

        // Tren pemasukan/pengeluaran per bulan (untuk grafik mini 12 bulan)
        $trenBulanan = Transaksi::whereBetween('tanggal', [$startOfYear, $endOfYear])
            ->selectRaw('MONTH(tanggal) AS bulan')
            ->selectRaw("SUM(CASE WHEN jenis = 'pemasukan' THEN nominal ELSE 0 END) AS masuk")
            ->selectRaw("SUM(CASE WHEN jenis = 'pengeluaran' THEN nominal ELSE 0 END) AS keluar")
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $bulanTren = [];
        for ($i = 1; $i <= 12; $i++) {
            $row = $trenBulanan->get($i);
            $bulanTren[$i] = [
                'masuk' => (float) ($row->masuk ?? 0),
                'keluar' => (float) ($row->keluar ?? 0),
            ];
        }
        $maxTren = max(1, max(array_column($bulanTren, 'masuk')), max(array_column($bulanTren, 'keluar')));

        return view('dashboard.index', compact(
            'year', 'hariIni', 'bulanIni', 'tahunIni', 'target',
            'piutang', 'hutang', 'tahunTersedia', 'recentTransaksis',
            'tahunMasuk', 'tahunKeluar', 'targetNominal', 'targetPersen',
            'persenPemasukan', 'persenPengeluaran',
            'selectedHasData', 'latestDataYear', 'bulanTren', 'maxTren'
        ));
    }
}
