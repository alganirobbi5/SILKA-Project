<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransaksiRequest;
use App\Http\Requests\UpdateTransaksiRequest;
use App\Models\Coa;
use App\Models\Kategori;
use App\Models\Transaksi;
use App\Services\TransaksiSaldoService;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    protected $saldoService;

    public function __construct(TransaksiSaldoService $saldoService)
    {
        $this->saldoService = $saldoService;
    }

    public function index(Request $request)
    {
        $query = Transaksi::with(['kategori', 'coa'])
            ->filterTanggal($request->tanggal_awal, $request->tanggal_akhir)
            ->filterJenis($request->jenis)
            ->filterKategori($request->kategori_id)
            ->filterCoa($request->coa_id)
            ->cariKeterangan($request->search);

        $transaksis = $query->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        $kategoris = Kategori::orderBy('kategori')->get();
        $coas = Coa::orderBy('kode_coa')->get();

        return view('transaksi.index', compact('transaksis', 'kategoris', 'coas'));
    }

    public function create()
    {
        $kategoris = Kategori::orderBy('kategori')->get();
        $coas = Coa::orderBy('kode_coa')->get();

        return view('transaksi.create', compact('kategoris', 'coas'));
    }

    public function store(StoreTransaksiRequest $request)
    {
        try {
            $this->saldoService->create($request->validated());
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    public function edit(Transaksi $transaksi)
    {
        $kategoris = Kategori::orderBy('kategori')->get();
        $coas = Coa::orderBy('kode_coa')->get();

        return view('transaksi.edit', compact('transaksi', 'kategoris', 'coas'));
    }

    public function update(UpdateTransaksiRequest $request, Transaksi $transaksi)
    {
        try {
            $this->saldoService->update($transaksi, $request->validated());
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage());
        }

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaksi $transaksi)
    {
        try {
            $this->saldoService->delete($transaksi);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }
}
