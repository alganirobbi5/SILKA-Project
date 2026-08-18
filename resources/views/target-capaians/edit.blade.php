@extends('layouts.app')

@section('title', 'Edit Target Capaian')

@section('content')
    <div class="card">
        <div class="card-header"><span>Edit Target Capaian Tahun {{ $targetCapaian->tahun }}</span></div>
        <div class="card-body">
            <form method="POST" action="{{ route('target-capaians.update', $targetCapaian->id) }}">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group">
                        <label for="tahun">Tahun</label>
                        <input type="number" min="2000" max="2100" class="form-control @error('tahun') input-error @enderror"
                               id="tahun" name="tahun" value="{{ old('tahun', $targetCapaian->tahun) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="target_capaian">Target Capaian (Rp)</label>
                        <input type="text" inputmode="decimal" class="form-control @error('target_capaian') input-error @enderror"
                               id="target_capaian" name="target_capaian" value="{{ old('target_capaian', $targetCapaian->target_capaian) }}" required>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('target-capaians.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection