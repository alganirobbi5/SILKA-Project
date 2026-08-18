@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
    <div class="card">
        <div class="card-header">
            <span>Edit Transaksi #{{ $transaksi->id }}</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('transaksi.update', $transaksi->id) }}">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" class="form-control @error('tanggal') input-error @enderror"
                               id="tanggal" name="tanggal" value="{{ old('tanggal', $transaksi->tanggal->format('Y-m-d')) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="jenis">Jenis</label>
                        <select name="jenis" id="jenis" class="form-control @error('jenis') input-error @enderror" required>
                            <option value="pemasukan" {{ old('jenis', $transaksi->jenis) == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                            <option value="pengeluaran" {{ old('jenis', $transaksi->jenis) == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="kategori_id">Kategori</label>
                        <select name="kategori_id" id="kategori_id" class="form-control @error('kategori_id') input-error @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($kategoris as $kategori)
                                <option value="{{ $kategori->id }}" {{ old('kategori_id', $transaksi->kategori_id) == $kategori->id ? 'selected' : '' }}>{{ $kategori->kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="coa_id">Akun COA</label>
                        <select name="coa_id" id="coa_id" class="form-control @error('coa_id') input-error @enderror" required>
                            <option value="">-- Pilih Akun COA --</option>
                            @foreach ($coas as $coa)
                                <option value="{{ $coa->id }}" {{ old('coa_id', $transaksi->coa_id) == $coa->id ? 'selected' : '' }}>{{ $coa->kode_coa }} - {{ $coa->nama_coa }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nominal">Nominal (Rp)</label>
                        <input type="text" inputmode="decimal" class="form-control @error('nominal') input-error @enderror"
                               id="nominal" name="nominal" value="{{ old('nominal', $transaksi->nominal) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <input type="text" class="form-control @error('keterangan') input-error @enderror"
                               id="keterangan" name="keterangan" value="{{ old('keterangan', $transaksi->keterangan) }}" maxlength="1000" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
