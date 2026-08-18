<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - SILKA Keuangan</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 12px;
            color: #111;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #111;
            padding-bottom: 10px;
        }
        .header h1 { font-size: 18px; }
        .header .periode { font-size: 13px; margin-top: 4px; }
        .no-print { text-align: right; margin-bottom: 10px; }
        .no-print button {
            padding: 6px 14px;
            cursor: pointer;
            font-size: 13px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        th, td {
            border: 1px solid #555;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        th { background: #eee; }
        .right { text-align: right; white-space: nowrap; }
        .total-row td { font-weight: bold; background: #f5f5f5; }
        .footer {
            margin-top: 24px;
            text-align: right;
            font-size: 11px;
            color: #444;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Cetak</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    <div class="header">
        <h1>Laporan Transaksi</h1>
        <div class="periode">
            @if ($filters['tanggal_awal'] || $filters['tanggal_akhir'])
                Periode:
                {{ $filters['tanggal_awal'] ? \Carbon\Carbon::parse($filters['tanggal_awal'])->format('d-m-Y') : 'Awal' }}
                s/d
                {{ $filters['tanggal_akhir'] ? \Carbon\Carbon::parse($filters['tanggal_akhir'])->format('d-m-Y') : 'Akhir' }}
            @else
                Periode: Semua Waktu
            @endif
            @if ($kategori)
                &nbsp;|&nbsp; Kategori: {{ $kategori->kategori }}
            @else
                &nbsp;|&nbsp; Kategori: Semua
            @endif
            @if ($filters['jenis'])
                &nbsp;|&nbsp; Jenis: {{ ucfirst($filters['jenis']) }}
            @endif
        </div>
    </div>

    @if ($transaksis->isEmpty())
        <p>Tidak ada transaksi yang cocok dengan filter.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width:30px">No</th>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Kategori</th>
                    <th>Kode COA</th>
                    <th>Nama COA</th>
                    <th>Keterangan</th>
                    <th class="right">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaksis as $index => $transaksi)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $transaksi->tanggal->format('d-m-Y') }}</td>
                        <td>{{ ucfirst($transaksi->jenis) }}</td>
                        <td>{{ $transaksi->kategori->kategori ?? '-' }}</td>
                        <td>{{ $transaksi->coa->kode_coa ?? '-' }}</td>
                        <td>{{ $transaksi->coa->nama_coa ?? '-' }}</td>
                        <td>{{ $transaksi->keterangan }}</td>
                        <td class="right">{{ number_format((float) $transaksi->nominal, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table style="width:40%;margin-left:auto">
            <tr class="total-row">
                <td>Total Pemasukan</td>
                <td class="right">{{ number_format($totals['pemasukan'], 2, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>Total Pengeluaran</td>
                <td class="right">{{ number_format($totals['pengeluaran'], 2, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>Selisih Bersih</td>
                <td class="right">{{ number_format($totals['selisih'], 2, ',', '.') }}</td>
            </tr>
        </table>
    @endif

    <div class="footer">
        Dicetak pada {{ now()->format('d-m-Y H:i') }}
    </div>
</body>
</html>