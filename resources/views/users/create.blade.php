@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
    <div class="page-header">
        <div>
            <div class="page-kicker">Manajemen Pengguna</div>
            <h2 class="page-title">Tambah User</h2>
            <p class="page-sub">Buat akun baru untuk mengakses aplikasi.</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" class="form-control @error('name') input-error @enderror"
                               id="name" name="name" value="{{ old('name') }}" maxlength="255" placeholder="Nama lengkap" required>
                        @error('name')
                            <span class="error-text">@include('partials.icon', ['name' => 'info', 'size' => 13]) {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control @error('email') input-error @enderror"
                               id="email" name="email" value="{{ old('email') }}" maxlength="255" placeholder="nama@email.com" required>
                        @error('email')
                            <span class="error-text">@include('partials.icon', ['name' => 'info', 'size' => 13]) {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control @error('password') input-error @enderror"
                               id="password" name="password" minlength="8" placeholder="Minimal 8 karakter" required>
                        @error('password')
                            <span class="error-text">@include('partials.icon', ['name' => 'info', 'size' => 13]) {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="level">Level</label>
                        <select name="level" id="level" class="form-control @error('level') input-error @enderror" required>
                            <option value="bendahara" {{ old('level') == 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                            <option value="admin" {{ old('level') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('level')
                            <span class="error-text">@include('partials.icon', ['name' => 'info', 'size' => 13]) {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="foto">Foto (maks 2 MB, JPEG/PNG/WebP)</label>
                    <input type="file" class="form-control file-input @error('foto') input-error @enderror"
                           id="foto" name="foto" accept="image/jpeg,image/png,image/webp">
                    <div class="hint">Opsional. Foto akan ditampilkan sebagai avatar pengguna.</div>
                    @error('foto')
                        <span class="error-text">@include('partials.icon', ['name' => 'info', 'size' => 13]) {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">@include('partials.icon', ['name' => 'check', 'size' => 15]) Simpan</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection