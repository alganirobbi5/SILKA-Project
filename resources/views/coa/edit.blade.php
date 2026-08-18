@extends('layouts.app')

@section('title', 'Edit COA')

@section('content')
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
                    </div>
                    <div class="form-group">
                        <label for="nama_coa">Nama COA</label>
                        <input type="text" class="form-control @error('nama_coa') input-error @enderror"
                               id="nama_coa" name="nama_coa" value="{{ old('nama_coa', $coa->nama_coa) }}" maxlength="255" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="jenis">Jenis</label>
                        <input type="text" class="form-control @error('jenis') input-error @enderror"
                               id="jenis" name="jenis" value="{{ old('jenis', $coa->jenis) }}" maxlength="255" required>
                    </div>
                    <div class="form-group">
                        <label for="cluster">Cluster</label>
                        <select name="cluster" id="cluster" class="form-control @error('cluster') input-error @enderror">
                            <option value="">Tidak ada</option>
                            @foreach ($clusters as $cluster)
                                <option value="{{ $cluster->id_cluster }}" {{ old('cluster', $coa->cluster) == $cluster->id_cluster ? 'selected' : '' }}>{{ $cluster->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="alert" style="background:#f8fafc;border:1px solid #e2e8f0;color:#475569">
                    Saldo COA hanya dapat berubah melalui transaksi dan tidak dapat diedit langsung.
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('coa.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection