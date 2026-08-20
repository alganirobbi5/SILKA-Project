@extends('layouts.app')

@section('title', 'COA')

@section('content')
    <div class="page-header">
        <div>
            <div class="page-kicker">Master Akun</div>
            <h2 class="page-title">Daftar Chart of Accounts</h2>
            <p class="page-sub">Kelola akun-akun keuangan SILKA.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('coa.create') }}" class="btn btn-primary">
                @include('partials.icon', ['name' => 'plus', 'size' => 16]) Tambah COA
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="filter-bar">
                <form method="GET" action="{{ route('coa.index') }}">
                    <div class="form-group">
                        <label for="search">Cari Kode / Nama</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci...">
                    </div>
                    <div class="form-group">
                        <label for="cluster">Cluster</label>
                        <select name="cluster" id="cluster" class="form-control">
                            <option value="">Semua</option>
                            @foreach ($clusters as $cluster)
                                <option value="{{ $cluster->id_cluster }}" {{ request('cluster') == $cluster->id_cluster ? 'selected' : '' }}>{{ $cluster->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-secondary">@include('partials.icon', ['name' => 'filter', 'size' => 15]) Filter</button>
                        <a href="{{ route('coa.index') }}" class="btn btn-link">Reset</a>
                    </div>
                </form>
            </div>

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama COA</th>
                            <th>Jenis</th>
                            <th>Cluster</th>
                            <th class="text-right">Saldo</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($coas as $coa)
                            <tr>
                                <td><span class="badge badge-brand">{{ $coa->kode_coa }}</span></td>
                                <td class="cell-main">{{ $coa->nama_coa }}</td>
                                <td>
                                    @php
                                        switch ($coa->jenis) {
                                            case 'Aset':
                                                $badge = 'badge-brand';
                                                break;
                                            case 'Liabilitas':
                                                $badge = 'badge-amber';
                                                break;
                                            case 'Ekuitas':
                                                $badge = 'badge-neutral';
                                                break;
                                            case 'Pendapatan':
                                                $badge = 'badge-masuk';
                                                break;
                                            case 'Beban':
                                                $badge = 'badge-keluar';
                                                break;
                                            default:
                                                $badge = 'badge-neutral';
                                        }
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ $coa->jenis }}</span>
                                </td>
                                <td>{{ $coa->clusterModel->nama ?? '-' }}</td>
                                <td class="nominal {{ $coa->saldo < 0 ? 'neg' : 'pos' }}">{{ rupiah($coa->saldo) }}</td>
                                <td class="text-center">
                                    <span class="action-cell">
                                        <a href="{{ route('coa.edit', $coa->id) }}" class="action-btn">
                                            @include('partials.icon', ['name' => 'edit', 'size' => 14]) Edit
                                        </a>
                                        <form method="POST" action="{{ route('coa.destroy', $coa->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus COA ini?');">
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
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div class="empty-icon">@include('partials.icon', ['name' => 'coa', 'size' => 28])</div>
                                        <p>Belum ada data COA.</p>
                                        <a href="{{ route('coa.create') }}" class="btn btn-primary">Tambah COA</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $coas->links() }}
        </div>
    </div>
@endsection