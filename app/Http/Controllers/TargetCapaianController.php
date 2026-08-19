<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTargetCapaianRequest;
use App\Http\Requests\UpdateTargetCapaianRequest;
use App\Models\TargetCapaian;
use App\Models\Transaksi;
use App\Support\PdfRenderer;
use Illuminate\Support\Facades\DB;

class TargetCapaianController extends Controller
{
    public function index()
    {
        $targets = TargetCapaian::orderBy('tahun', 'desc')->paginate(20);

        return view('target-capaians.index', compact('targets'));
    }

    public function pdf()
    {
        $targets = TargetCapaian::orderBy('tahun', 'desc')->get();

        $tahuns = $targets->pluck('tahun')->filter()->unique()->values()->all();

        $realisasiByTahun = Transaksi::where('jenis', 'pemasukan')
            ->whereIn(DB::raw('YEAR(tanggal)'), $tahuns)
            ->selectRaw('YEAR(tanggal) as tahun, SUM(nominal) as total')
            ->groupBy(DB::raw('YEAR(tanggal)'))
            ->pluck('total', 'tahun')
            ->map(fn ($value) => (float) $value);

        $html = view('target-capaians.pdf', compact('targets', 'realisasiByTahun'))->render();

        return PdfRenderer::render($html, 'target-capaian-' . date('Y-m-d') . '.pdf');
    }

    public function create()
    {
        return view('target-capaians.create');
    }

    public function store(StoreTargetCapaianRequest $request)
    {
        TargetCapaian::create($request->validated());

        return redirect()->route('target-capaians.index')
            ->with('success', 'Target capaian berhasil ditambahkan.');
    }

    public function edit(TargetCapaian $targetCapaian)
    {
        return view('target-capaians.edit', compact('targetCapaian'));
    }

    public function update(UpdateTargetCapaianRequest $request, TargetCapaian $targetCapaian)
    {
        $targetCapaian->update($request->validated());

        return redirect()->route('target-capaians.index')
            ->with('success', 'Target capaian berhasil diperbarui.');
    }

    public function destroy(TargetCapaian $targetCapaian)
    {
        $targetCapaian->delete();

        return redirect()->route('target-capaians.index')
            ->with('success', 'Target capaian berhasil dihapus.');
    }
}
