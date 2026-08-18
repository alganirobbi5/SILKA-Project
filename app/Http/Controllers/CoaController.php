<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCoaRequest;
use App\Http\Requests\UpdateCoaRequest;
use App\Models\Cluster;
use App\Models\Coa;
use Illuminate\Http\Request;

class CoaController extends Controller
{
    public function index(Request $request)
    {
        $clusters = Cluster::orderBy('id_cluster')->get();

        $query = Coa::with('clusterModel')
            ->search($request->search)
            ->byCluster($request->cluster);

        $coas = $query->orderBy('kode_coa')->paginate(20)->withQueryString();

        return view('coa.index', compact('coas', 'clusters'));
    }

    public function create()
    {
        $clusters = Cluster::orderBy('id_cluster')->get();

        return view('coa.create', compact('clusters'));
    }

    public function store(StoreCoaRequest $request)
    {
        Coa::create([
            'kode_coa' => $request->kode_coa,
            'nama_coa' => $request->nama_coa,
            'jenis' => $request->jenis,
            'cluster' => $request->cluster,
            'saldo' => $request->saldo_awal ?? 0,
        ]);

        return redirect()->route('coa.index')
            ->with('success', 'COA berhasil ditambahkan.');
    }

    public function edit(Coa $coa)
    {
        $clusters = Cluster::orderBy('id_cluster')->get();

        return view('coa.edit', compact('coa', 'clusters'));
    }

    public function update(UpdateCoaRequest $request, Coa $coa)
    {
        $coa->update($request->safe()->only(['kode_coa', 'nama_coa', 'jenis', 'cluster']));

        return redirect()->route('coa.index')
            ->with('success', 'COA berhasil diperbarui.');
    }

    public function destroy(Coa $coa)
    {
        $hasTransaksi = $coa->transaksis()->exists();
        if ($hasTransaksi) {
            return back()->with('error', 'COA yang masih digunakan transaksi tidak dapat dihapus.');
        }

        if ((float) $coa->saldo != 0) {
            return back()->with('error', 'COA dengan saldo tidak nol tidak dapat dihapus.');
        }

        try {
            $coa->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus COA: ' . $e->getMessage());
        }

        return redirect()->route('coa.index')
            ->with('success', 'COA berhasil dihapus.');
    }
}
