@extends('layouts.app')

@section('title', 'COA')

@section('content')
    <div class="card">
        <div class="card-header">
            <span>Daftar Chart of Accounts</span>
            <a href="{{ route('coa.create') }}" class="btn btn-primary">Tambah COA</a>
        </div>
        <div class="card-body">
            <div class="filter-bar">
                <form method="GET" action="{{ route('coa.index') }}">
                    <div class="form-group">
                        <label for="search">Cari Kode / Nama</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}">
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
                    <button type="submit" class="btn btn-secondary">Filter</button>
                    <a href="{{ route('coa.index') }}" class="btn btn-link">Reset</a>
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
                                <td>{{ $coa->kode_coa }}</td>
                                <td>{{ $coa->nama_coa }}</td>
                                <td>{{ $coa->jenis }}</td>
                                <td>{{ $coa->clusterModel->nama ?? '-' }}</td>
                                <td class="nominal">{{ rupiah($coa->saldo) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('coa.edit', $coa->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('coa.destroy', $coa->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus COA ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
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