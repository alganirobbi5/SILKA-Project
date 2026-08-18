@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
    <div class="card">
        <div class="card-header"><span>Tambah Kategori</span></div>
        <div class="card-body">
            <form method="POST" action="{{ route('kategori.store') }}">
                @csrf
                <div class="form-group">
                    <label for="kategori">Nama Kategori</label>
                    <input type="text" class="form-control @error('kategori') input-error @enderror"
                           id="kategori" name="kategori" value="{{ old('kategori') }}" maxlength="150" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('kategori.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection