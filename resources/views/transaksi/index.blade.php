@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
    <div class="card">
        <div class="card-header">
            <span>Daftar Transaksi</span>
            <a href="{{ route('transaksi.create') }}" class="btn btn-primary">Tambah Transaksi</a>
        </div>
        <div class="card-body">
            <div class="filter-bar">
                <form method="GET" action="{{ route('transaksi.index') }}">
                    <div class="form-group">
                        <label for="search">Cari Keterangan</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}">
                    </div>
                    <div class="form-group">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" value="{{ request('tanggal_awal') }}">
                    </div>
                    <div class="form-group">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}">
                    </div>
                    <div class="form-group">
                        <label for="jenis">Jenis</label>
                        <select name="jenis" id="jenis" class="form-control">
                            <option value="">Semua</option>
                            <option value="pemasukan" {{ request('jenis') == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                            <option value="pengeluaran" {{ request('jenis') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kategori_id">Kategori</label>
                        <select name="kategori_id" id="kategori_id" class="form-control">
                            <option value="">Semua</option>
                            @foreach ($kategoris as $kategori)
                                <option value="{{ $kategori->id }}" {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>{{ $kategori->kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="coa_id">Akun COA</label>
                        <select name="coa_id" id="coa_id" class="form-control">
                            <option value="">Semua</option>
                            @foreach ($coas as $coa)
                                <option value="{{ $coa->id }}" {{ request('coa_id') == $coa->id ? 'selected' : '' }}>{{ $coa->kode_coa }} - {{ $coa->nama_coa }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-secondary">Filter</button>
                    <a href="{{ route('transaksi.index') }}" class="btn btn-link">Reset</a>
                </form>
            </div>

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Kategori</th>
                            <th>COA</th>
                            <th class="text-right">Nominal</th>
                            <th>Keterangan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksis as $transaksi)
                            <tr>
                                <td>{{ $transaksi->tanggal->format('d-m-Y') }}</td>
                                <td>
                                    @if ($transaksi->isPemasukan())
                                        <span class="badge badge-masuk">Pemasukan</span>
                                    @else
                                        <span class="badge badge-keluar">Pengeluaran</span>
                                    @endif
                                </td>
                                <td>{{ $transaksi->kategori->kategori ?? '-' }}</td>
                                <td>
                                    @if ($transaksi->coa)
                                        {{ $transaksi->coa->kode_coa }} - {{ $transaksi->coa->nama_coa }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="nominal">{{ rupiah($transaksi->nominal) }}</td>
                                <td>{{ Str::limit($transaksi->keterangan, 50) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('transaksi.edit', $transaksi->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('transaksi.destroy', $transaksi->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus transaksi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <p>Belum ada data transaksi.</p>
                                        <a href="{{ route('transaksi.create') }}" class="btn btn-primary">Tambah Transaksi</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $transaksis->links() }}
        </div>
    </div>
@endsection
