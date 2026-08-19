<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi - SILKA Keuangan</title>
    <style>
        @include('partials.pdf-styles')
    </style>
</head>
<body>
    <div class="brand-band">
        <table>
            <tr>
                <td>
                    <div class="brand-word">SILKA</div>
                    <div class="brand-sub">Sistem Informasi Keuangan</div>
                </td>
                <td style="text-align:right">
                    <h1>Laporan Transaksi</h1>
                    <div class="band-sub">Rekap arus kas transaksi keuangan</div>
                </td>
            </tr>
        </table>
    </div>
    <div class="accent-bar"></div>

    <div class="chips">
        <span class="chip">Periode: {{ $filters['tanggal_awal'] || $filters['tanggal_akhir']
            ? trim(($filters['tanggal_awal'] ? \Carbon\Carbon::parse($filters['tanggal_awal'])->format('d-m-Y') : 'Awal') . ' s/d ' . ($filters['tanggal_akhir'] ? \Carbon\Carbon::parse($filters['tanggal_akhir'])->format('d-m-Y') : 'Akhir'))
            : 'Semua Waktu' }}</span>
        <span class="chip">Kategori: {{ $kategori->kategori ?? 'Semua' }}</span>
        @if ($filters['jenis'])
            <span class="chip">Jenis: {{ ucfirst($filters['jenis']) }}</span>
        @endif
    </div>

    <div class="meta">Dicetak pada {{ now()->format('d-m-Y H:i') }} &nbsp;&bull;&nbsp; {{ $transaksis->count() }} transaksi</div>

    @if ($transaksis->isEmpty())
        <div class="empty-box">Tidak ada transaksi yang cocok dengan filter yang dipilih.</div>
    @else
        <table class="summary">
            <tr>
                <td>
                    <div class="sum-card emerald">
                        <div class="lbl">Total Pemasukan</div>
                        <div class="val emerald">{{ rupiah($totals['pemasukan']) }}</div>
                        <div class="note">Arus kas masuk</div>
                    </div>
                </td>
                <td>
                    <div class="sum-card rose">
                        <div class="lbl">Total Pengeluaran</div>
                        <div class="val rose">{{ rupiah($totals['pengeluaran']) }}</div>
                        <div class="note">Arus kas keluar</div>
                    </div>
                </td>
                <td>
                    <div class="sum-card {{ $totals['selisih'] < 0 ? 'rose' : 'indigo' }}">
                        <div class="lbl">Selisih Bersih</div>
                        <div class="val {{ $totals['selisih'] < 0 ? 'rose' : 'indigo' }}">{{ rupiah($totals['selisih']) }}</div>
                        <div class="note">Pemasukan &minus; Pengeluaran</div>
                    </div>
                </td>
                <td>
                    <div class="sum-card navy">
                        <div class="lbl">Jumlah Transaksi</div>
                        <div class="val navy">{{ $transaksis->count() }}</div>
                        <div class="note">Total entri tercatat</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="sec-title"><span class="sec-line"></span>Rincian Transaksi</div>

        <table class="data">
            <thead>
                <tr>
                    <th style="width:24px">No</th>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Kategori</th>
                    <th>Kode COA</th>
                    <th>Nama COA</th>
                    <th>Keterangan</th>
                    <th class="r">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaksis as $index => $transaksi)
                    <tr class="{{ $index % 2 === 1 ? 'alt' : '' }}">
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $transaksi->tanggal->format('d-m-Y') }}</td>
                        <td>
                            @if ($transaksi->isPemasukan())
                                <span class="badge masuk">Pemasukan</span>
                            @else
                                <span class="badge keluar">Pengeluaran</span>
                            @endif
                        </td>
                        <td>{{ $transaksi->kategori->kategori ?? '-' }}</td>
                        <td>{{ $transaksi->coa->kode_coa ?? '-' }}</td>
                        <td>{{ $transaksi->coa->nama_coa ?? '-' }}</td>
                        <td>{{ $transaksi->keterangan }}</td>
                        <td class="r">{{ rupiah($transaksi->nominal) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="data" style="width:55%;margin-left:auto">
            <tr>
                <td>Total Pemasukan</td>
                <td class="r">{{ rupiah($totals['pemasukan']) }}</td>
            </tr>
            <tr>
                <td>Total Pengeluaran</td>
                <td class="r">{{ rupiah($totals['pengeluaran']) }}</td>
            </tr>
            <tr class="total">
                <td>Selisih Bersih</td>
                <td class="r">{{ rupiah($totals['selisih']) }}</td>
            </tr>
        </table>

        <table class="sig">
            <tr>
                <td>
                    <div class="sig-label">Mengetahui,</div>
                    <div class="sig-label" style="margin-top:2px">Bendahara</div>
                    <div class="sig-line">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
                </td>
                <td>
                    <div class="sig-label">Mengetahui,</div>
                    <div class="sig-label" style="margin-top:2px">Kepala / Pimpinan</div>
                    <div class="sig-line">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
                </td>
            </tr>
        </table>
    @endif
</body>
</html>