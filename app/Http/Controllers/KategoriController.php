<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKategoriRequest;
use App\Http\Requests\UpdateKategoriRequest;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::orderBy('kategori')->paginate(20);

        return view('kategori.index', compact('kategoris'));
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function store(StoreKategoriRequest $request)
    {
        Kategori::create($request->validated());

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Kategori $kategori)
    {
        return view('kategori.edit', compact('kategori'));
    }

    public function update(UpdateKategoriRequest $request, Kategori $kategori)
    {
        $kategori->update($request->validated());

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->id === 1) {
            return back()->with('error', 'Kategori default tidak dapat dihapus.');
        }

        try {
            DB::transaction(function () use ($kategori) {
                \App\Models\Transaksi::where('kategori_id', $kategori->id)
                    ->update(['kategori_id' => 1]);

                $kategori->delete();
            }, 3);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil dihapus dan transaksi dipindahkan ke kategori default.');
    }
}
