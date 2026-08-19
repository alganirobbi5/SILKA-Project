@extends('layouts.app')

@section('title', 'Edit Target Capaian')

@section('content')
    <div class="page-header">
        <div>
            <div class="page-kicker">Perencanaan</div>
            <h2 class="page-title">Edit Target Capaian Tahun {{ $targetCapaian->tahun }}</h2>
            <p class="page-sub">Perbarui target pemasukan tahun ini.</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('target-capaians.update', $targetCapaian->id) }}">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group">
                        <label for="tahun">Tahun</label>
                        <input type="number" min="2000" max="2100" class="form-control @error('tahun') input-error @enderror"
                               id="tahun" name="tahun" value="{{ old('tahun', $targetCapaian->tahun) }}" required>
                        @error('tahun')
                            <span class="error-text">@include('partials.icon', ['name' => 'info', 'size' => 13]) {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="target_capaian">Target Capaian (Rp)</label>
                        <input type="text" inputmode="decimal" class="form-control @error('target_capaian') input-error @enderror"
                               id="target_capaian" name="target_capaian" value="{{ old('target_capaian', $targetCapaian->target_capaian) }}" placeholder="0" required>
                        @error('target_capaian')
                            <span class="error-text">@include('partials.icon', ['name' => 'info', 'size' => 13]) {{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">@include('partials.icon', ['name' => 'check', 'size' => 15]) Simpan Perubahan</button>
                    <a href="{{ route('target-capaians.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection