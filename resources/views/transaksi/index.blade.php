@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
    <div class="page-header">
        <div>
            <div class="page-kicker">Catatan Keuangan</div>
            <h2 class="page-title">Daftar Transaksi</h2>
            <p class="page-sub">Kelola seluruh pemasukan dan pengeluaran SILKA.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('transaksi.create') }}" class="btn btn-primary">
                @include('partials.icon', ['name' => 'plus', 'size' => 16]) Tambah Transaksi
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="filter-bar">
                <form method="GET" action="{{ route('transaksi.index') }}">
                    <div class="form-group">
                        <label for="search">Cari Keterangan</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci...">
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
                    <div class="form-actions">
                        <button type="submit" class="btn btn-secondary">@include('partials.icon', ['name' => 'filter', 'size' => 15]) Filter</button>
                        <a href="{{ route('transaksi.index') }}" class="btn btn-link">Reset</a>
                    </div>
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
                                <td>
                                    <div class="cell-main">{{ $transaksi->tanggal->format('d M Y') }}</div>
                                    <div class="cell-sub">{{ $transaksi->tanggal->format('H:i') }}</div>
                                </td>
                                <td>
                                    @if ($transaksi->isPemasukan())
                                        <span class="badge badge-masuk">@include('partials.icon', ['name' => 'trend-up', 'size' => 12]) Pemasukan</span>
                                    @else
                                        <span class="badge badge-keluar">@include('partials.icon', ['name' => 'trend-down', 'size' => 12]) Pengeluaran</span>
                                    @endif
                                </td>
                                <td>{{ $transaksi->kategori->kategori ?? '-' }}</td>
                                <td>
                                    @if ($transaksi->coa)
                                        <span class="badge badge-neutral">{{ $transaksi->coa->kode_coa }}</span>
                                        {{ $transaksi->coa->nama_coa }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="nominal {{ $transaksi->isPemasukan() ? 'pos' : 'neg' }}">{{ rupiah($transaksi->nominal) }}</td>
                                <td>{{ Str::limit($transaksi->keterangan, 50) }}</td>
                                <td class="text-center">
                                    <span class="action-cell">
                                        <a href="{{ route('transaksi.edit', $transaksi->id) }}" class="action-btn">
                                            @include('partials.icon', ['name' => 'edit', 'size' => 14]) Edit
                                        </a>
                                        <form method="POST" action="{{ route('transaksi.destroy', $transaksi->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus transaksi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn danger">
                                                @include('partials.icon', ['name' => 'trash', 'size' => 14]) Hapus
                                            </button>
                                        </form>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-icon">@include('partials.icon', ['name' => 'inbox', 'size' => 28])</div>
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