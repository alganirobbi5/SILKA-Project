@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
    <div class="page-header">
        <div>
            <div class="page-kicker">Master Kategori</div>
            <h2 class="page-title">Tambah Kategori</h2>
            <p class="page-sub">Buat kategori transaksi baru.</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('kategori.store') }}">
                @csrf
                <div class="form-group">
                    <label for="kategori">Nama Kategori</label>
                    <input type="text" class="form-control @error('kategori') input-error @enderror"
                           id="kategori" name="kategori" value="{{ old('kategori') }}" maxlength="150" placeholder="Contoh: Iuran Kas" required>
                    @error('kategori')
                        <span class="error-text">@include('partials.icon', ['name' => 'info', 'size' => 13]) {{ $message }}</span>
                    @enderror
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">@include('partials.icon', ['name' => 'check', 'size' => 15]) Simpan</button>
                    <a href="{{ route('kategori.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection