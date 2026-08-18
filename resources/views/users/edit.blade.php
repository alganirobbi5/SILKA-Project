@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <div class="card">
        <div class="card-header"><span>Edit User: {{ $user->name }}</span></div>
        <div class="card-body">
            <form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" class="form-control @error('name') input-error @enderror"
                               id="name" name="name" value="{{ old('name', $user->name) }}" maxlength="255" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control @error('email') input-error @enderror"
                               id="email" name="email" value="{{ old('email', $user->email) }}" maxlength="255" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password Baru (kosongkan jika tidak diubah)</label>
                        <input type="password" class="form-control @error('password') input-error @enderror"
                               id="password" name="password" minlength="8">
                    </div>
                    <div class="form-group">
                        <label for="level">Level</label>
                        <select name="level" id="level" class="form-control @error('level') input-error @enderror" required>
                            <option value="bendahara" {{ old('level', $user->level) == 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                            <option value="admin" {{ old('level', $user->level) == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="foto">Foto (maks 2 MB, JPEG/PNG/WebP)</label>
                    @if ($user->foto)
                        <div style="margin-bottom:8px">
                            <img src="{{ $user->foto_url }}" alt="Foto {{ $user->name }}" style="width:80px;height:80px;object-fit:cover;border-radius:8px">
                        </div>
                    @endif
                    <input type="file" class="form-control @error('foto') input-error @enderror"
                           id="foto" name="foto" accept="image/jpeg,image/png,image/webp">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection