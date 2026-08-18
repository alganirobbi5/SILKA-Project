@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
    <div class="card">
        <div class="card-header"><span>Filter Laporan</span></div>
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.index') }}">
                <div class="form-row">
                    <div class="form-group">
                        <label for="kategori_id">Kategori</label>
                        <select name="kategori_id" id="kategori_id" class="form-control">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategoris as $kategori)
                                <option value="{{ $kategori->id }}" {{ $filters['kategori_id'] == $kategori->id ? 'selected' : '' }}>{{ $kategori->kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jenis">Jenis</label>
                        <select name="jenis" id="jenis" class="form-control">
                            <option value="">Semua Jenis</option>
                            <option value="pemasukan" {{ $filters['jenis'] == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                            <option value="pengeluaran" {{ $filters['jenis'] == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" value="{{ $filters['tanggal_awal'] }}">
                    </div>
                    <div class="form-group">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" value="{{ $filters['tanggal_akhir'] }}">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Tampilkan Laporan</button>
                    <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    @if ($hasFilter)
        <div class="card">
            <div class="card-header">
                <span>Hasil Laporan</span>
                <span class="no-print">
                    <a href="{{ route('laporan.print', request()->query()) }}" class="btn btn-secondary btn-sm" target="_blank">Cetak</a>
                    <a href="{{ route('laporan.export', request()->query()) }}" class="btn btn-primary btn-sm">Export Excel</a>
                </span>
            </div>
            <div class="card-body">
                @if ($transaksis->isEmpty())
                    <div class="empty-state"><p>Tidak ada transaksi yang cocok dengan filter.</p></div>
                @else
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Kategori</th>
                                    <th>COA</th>
                                    <th>Keterangan</th>
                                    <th class="text-right">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transaksis as $index => $transaksi)
                                    <tr>
                                        <td>{{ $transaksis->firstItem() + $index }}</td>
                                        <td>{{ $transaksi->tanggal->format('d-m-Y') }}</td>
                                        <td>
                                            @if ($transaksi->isPemasukan())
                                                <span class="badge badge-masuk">Pemasukan</span>
                                            @else
                                                <span class="badge badge-keluar">Pengeluaran</span>
                                            @endif
                                        </td>
                                        <td>{{ $transaksi->kategori->kategori ?? '-' }}</td>
                                        <td>{{ $transaksi->coa ? $transaksi->coa->kode_coa . ' - ' . $transaksi->coa->nama_coa : '-' }}</td>
                                        <td>{{ Str::limit($transaksi->keterangan, 60) }}</td>
                                        <td class="nominal">{{ rupiah($transaksi->nominal) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $transaksis->links() }}
                @endif
            </div>
        </div>

        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-label">Total Pemasukan</div>
                <div class="stat-value green">{{ rupiah($totals['pemasukan']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Pengeluaran</div>
                <div class="stat-value red">{{ rupiah($totals['pengeluaran']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Selisih Bersih</div>
                <div class="stat-value">{{ rupiah($totals['selisih']) }}</div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="empty-state">
                    <p>Pilih filter kategori dan rentang tanggal untuk melihat laporan.</p>
                </div>
            </div>
        </div>
    @endif
@endsection