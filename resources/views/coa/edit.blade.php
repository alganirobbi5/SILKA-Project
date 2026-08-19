@extends('layouts.app')

@section('title', 'Edit COA')

@section('content')
    <div class="page-header">
        <div>
            <div class="page-kicker">Master Akun</div>
            <h2 class="page-title">Edit COA</h2>
            <p class="page-sub">Perbarui data akun chart of accounts.</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span>Edit COA</span>
            <span class="nominal">Saldo saat ini: {{ rupiah($coa->saldo) }}</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('coa.update', $coa->id) }}">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group">
                        <label for="kode_coa">Kode COA</label>
                        <input type="text" class="form-control @error('kode_coa') input-error @enderror"
                               id="kode_coa" name="kode_coa" value="{{ old('kode_coa', $coa->kode_coa) }}" maxlength="255" required>
                        @error('kode_coa')
                            <span class="error-text">@include('partials.icon', ['name' => 'info', 'size' => 13]) {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="nama_coa">Nama COA</label>
                        <input type="text" class="form-control @error('nama_coa') input-error @enderror"
                               id="nama_coa" name="nama_coa" value="{{ old('nama_coa', $coa->nama_coa) }}" maxlength="255" required>
                        @error('nama_coa')
                            <span class="error-text">@include('partials.icon', ['name' => 'info', 'size' => 13]) {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="jenis">Jenis</label>
                        <input type="text" class="form-control @error('jenis') input-error @enderror"
                               id="jenis" name="jenis" value="{{ old('jenis', $coa->jenis) }}" maxlength="255"
                               list="jenis-options" required>
                        <datalist id="jenis-options">
                            <option value="Aset"></option>
                            <option value="Liabilitas"></option>
                            <option value="Ekuitas"></option>
                            <option value="Pendapatan"></option>
                            <option value="Beban"></option>
                        </datalist>
                        @error('jenis')
                            <span class="error-text">@include('partials.icon', ['name' => 'info', 'size' => 13]) {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="cluster">Cluster</label>
                        <select name="cluster" id="cluster" class="form-control @error('cluster') input-error @enderror">
                            <option value="">Tidak ada</option>
                            @foreach ($clusters as $cluster)
                                <option value="{{ $cluster->id_cluster }}" {{ old('cluster', $coa->cluster) == $cluster->id_cluster ? 'selected' : '' }}>{{ $cluster->nama }}</option>
                            @endforeach
                        </select>
                        @error('cluster')
                            <span class="error-text">@include('partials.icon', ['name' => 'info', 'size' => 13]) {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="alert alert-info mb-0">
                    @include('partials.icon', ['name' => 'info', 'size' => 18])
                    <span>Saldo COA hanya dapat berubah melalui transaksi dan tidak dapat diedit langsung.</span>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">@include('partials.icon', ['name' => 'check', 'size' => 15]) Simpan Perubahan</button>
                    <a href="{{ route('coa.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection