@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="card">
        <div class="card-header">
            <span>Ringkasan Tahun {{ $year }}</span>
            <form method="GET" action="{{ route('dashboard') }}" class="form-inline">
                <div class="form-group">
                    <label for="year" class="sr-only">Pilih Tahun</label>
                    <select name="year" id="year" class="form-control">
                        @foreach ($tahunTersedia as $tahun)
                            <option value="{{ $tahun }}" {{ $tahun == $year ? 'selected' : '' }}>{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-secondary">Tampilkan</button>
            </form>
        </div>
        <div class="card-body">
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-label">Pemasukan Hari Ini</div>
                    <div class="stat-value green">{{ rupiah($hariIni->pemasukan ?? 0) }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Pengeluaran Hari Ini</div>
                    <div class="stat-value red">{{ rupiah($hariIni->pengeluaran ?? 0) }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Pemasukan Bulan Ini</div>
                    <div class="stat-value green">{{ rupiah($bulanIni->pemasukan ?? 0) }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Pengeluaran Bulan Ini</div>
                    <div class="stat-value red">{{ rupiah($bulanIni->pengeluaran ?? 0) }}</div>
                </div>
            </div>

            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-label">Pemasukan Tahun {{ $year }}</div>
                    <div class="stat-value green">{{ rupiah($tahunIni->pemasukan ?? 0) }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Pengeluaran Tahun {{ $year }}</div>
                    <div class="stat-value red">{{ rupiah($tahunIni->pengeluaran ?? 0) }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Target Capaian {{ $year }}</div>
                    <div class="stat-value">{{ $target ? rupiah($target->target_capaian) : 'Belum ditentukan' }}</div>
                </div>
            </div>

            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-label">Piutang Tahun {{ $year - 1 }}</div>
                    <div class="stat-value">{{ rupiah($piutang) }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Hutang Tahun {{ $year - 1 }}</div>
                    <div class="stat-value">{{ rupiah($hutang) }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
    </style>
@endsection
