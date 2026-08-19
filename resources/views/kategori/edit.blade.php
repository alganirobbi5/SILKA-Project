@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
    <div class="page-header">
        <div>
            <div class="page-kicker">Master Kategori</div>
            <h2 class="page-title">Edit Kategori</h2>
            <p class="page-sub">Perbarui nama kategori transaksi.</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('kategori.update', $kategori->id) }}">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="kategori">Nama Kategori</label>
                    <input type="text" class="form-control @error('kategori') input-error @enderror"
                           id="kategori" name="kategori" value="{{ old('kategori', $kategori->kategori) }}" maxlength="150" required>
                    @error('kategori')
                        <span class="error-text">@include('partials.icon', ['name' => 'info', 'size' => 13]) {{ $message }}</span>
                    @enderror
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">@include('partials.icon', ['name' => 'check', 'size' => 15]) Simpan Perubahan</button>
                    <a href="{{ route('kategori.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection