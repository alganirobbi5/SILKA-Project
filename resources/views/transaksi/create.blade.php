@extends('layouts.app')

@section('title', 'Tambah Transaksi')

@section('content')
    <div class="page-header">
        <div>
            <div class="page-kicker">Catatan Keuangan</div>
            <h2 class="page-title">Tambah Transaksi</h2>
            <p class="page-sub">Catat pemasukan atau pengeluaran baru SILKA.</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('transaksi.store') }}">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" class="form-control @error('tanggal') input-error @enderror"
                               id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        @error('tanggal')
                            <span class="error-text">@include('partials.icon', ['name' => 'info', 'size' => 13]) {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="jenis">Jenis</label>
                        <select name="jenis" id="jenis" class="form-control @error('jenis') input-error @enderror" required>
                            <option value="pemasukan" {{ old('jenis') == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                            <option value="pengeluaran" {{ old('jenis') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                        </select>
                        @error('jenis')
                            <span class="error-text">@include('partials.icon', ['name' => 'info', 'size' => 13]) {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="kategori_id">Kategori</label>
                        <select name="kategori_id" id="kategori_id" class="form-control @error('kategori_id') input-error @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($kategoris as $kategori)
                                <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>{{ $kategori->kategori }}</option>
                            @endforeach
                        </select>
                        @error('kategori_id')
                            <span class="error-text">@include('partials.icon', ['name' => 'info', 'size' => 13]) {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="coa_id">Akun COA</label>
                        <select name="coa_id" id="coa_id" class="form-control @error('coa_id') input-error @enderror" required>
                            <option value="">-- Pilih Akun COA --</option>
                            @foreach ($coas as $coa)
                                <option value="{{ $coa->id }}" {{ old('coa_id') == $coa->id ? 'selected' : '' }}>{{ $coa->kode_coa }} - {{ $coa->nama_coa }}</option>
                            @endforeach
                        </select>
                        @error('coa_id')
                            <span class="error-text">@include('partials.icon', ['name' => 'info', 'size' => 13]) {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nominal">Nominal (Rp)</label>
                        <input type="text" inputmode="decimal" class="form-control @error('nominal') input-error @enderror"
                               id="nominal" name="nominal" value="{{ old('nominal') }}" placeholder="0" required>
                        @error('nominal')
                            <span class="error-text">@include('partials.icon', ['name' => 'info', 'size' => 13]) {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <input type="text" class="form-control @error('keterangan') input-error @enderror"
                               id="keterangan" name="keterangan" value="{{ old('keterangan') }}" maxlength="1000" placeholder="Contoh: Iuran kas bulanan" required>
                        @error('keterangan')
                            <span class="error-text">@include('partials.icon', ['name' => 'info', 'size' => 13]) {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">@include('partials.icon', ['name' => 'check', 'size' => 15]) Simpan</button>
                    <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection