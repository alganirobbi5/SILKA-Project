<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Target Capaian - SILKA</title>
    <style>
        @include('partials.pdf-styles')
    </style>
    <style>
        /* =========================================================
           Override khusus cetak A4 untuk Laporan Target Capaian
           (hanya file ini; tidak memengaruhi PDF laporan lain)
           ========================================================= */
        @page {
            size: A4 portrait;
            margin: 10mm 10mm 12mm 10mm;
            @bottom-left {
                content: "SILKA Keuangan";
                font-family: 'Segoe UI', 'DejaVu Sans', sans-serif;
                font-size: 7pt;
                color: #64748b;
            }
            @bottom-right {
                content: "Hal " counter(page) " dari " counter(pages);
                font-family: 'Segoe UI', 'DejaVu Sans', sans-serif;
                font-size: 7pt;
                color: #64748b;
            }
        }

        body {
            font-family: 'Segoe UI', 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #111827;
            line-height: 1.4;
        }

        /* ---------- Header ---------- */
        .brand-band {
            background: #0f172a;
            color: #ffffff;
            padding: 9pt 14pt;
            border-radius: 0;
        }
        .brand-band table { width: 100%; border-collapse: collapse; }
        .brand-band td { padding: 0; border: none; vertical-align: middle; }
        .brand-word { font-size: 18pt; font-weight: 700; letter-spacing: 0.5pt; }
        .brand-sub { font-size: 7pt; letter-spacing: 1.5pt; text-transform: uppercase; color: #cbd5e1; }
        .brand-band h1 { font-size: 17pt; font-weight: 700; text-align: right; line-height: 1.2; color: #ffffff; }
        .band-sub { font-size: 8pt; color: #cbd5e1; text-align: right; margin-top: 2pt; }

        .accent-bar {
            height: 3pt;
            background: #10b981;
            margin: 0 0 12pt;
            border-radius: 0;
        }

        /* ---------- Info laporan ---------- */
        table.info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12pt;
        }
        table.info td.info-cell {
            width: 25%;
            border: 0.7pt solid #d1d5db;
            padding: 7pt 10pt;
            vertical-align: top;
            background: #f9fafb;
        }
        .info-label {
            font-size: 7pt;
            text-transform: uppercase;
            letter-spacing: 0.6pt;
            color: #6b7280;
            font-weight: 700;
        }
        .info-val {
            font-size: 10pt;
            color: #111827;
            font-weight: 600;
            margin-top: 2pt;
            line-height: 1.4;
            display: block;
        }

        /* ---------- Summary cards ---------- */
        table.summary {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4pt 0;
            margin: 0 0 14pt;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        table.summary td { width: 25%; vertical-align: top; }
        .sum-card {
            border: 0.8pt solid #e5e7eb;
            border-top: 3pt solid #0f172a;
            border-radius: 4pt;
            padding: 11pt 10pt;
            background: #ffffff;
        }
        .sum-card .lbl {
            font-size: 7pt;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
            color: #6b7280;
            font-weight: 700;
        }
        .sum-card .val { font-size: 12pt; font-weight: 700; margin-top: 2pt; white-space: nowrap; }
        .sum-card .note { font-size: 7.5pt; color: #9ca3af; margin-top: 2pt; }
        .sum-card.emerald { border-top-color: #059669; }
        .sum-card .val.emerald { color: #065f46; }
        .sum-card.rose { border-top-color: #e11d48; }
        .sum-card .val.rose { color: #9f1239; }
        .sum-card.indigo { border-top-color: #4f46e5; }
        .sum-card .val.indigo { color: #4338ca; }
        .sum-card.navy { border-top-color: #0f172a; }
        .sum-card .val.navy { color: #0f172a; }

        /* ---------- Section title ---------- */
        .sec-title {
            font-size: 10pt;
            font-weight: 700;
            color: #111827;
            margin: 14pt 0 7pt;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
        }
        .sec-title .sec-line {
            display: inline-block;
            width: 3.5pt;
            height: 10pt;
            background: #059669;
            margin-right: 5pt;
            vertical-align: middle;
        }

        /* ---------- Tabel rincian ---------- */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10pt;
        }
        table.data thead { display: table-header-group; }
        table.data th {
            background: #0f172a;
            color: #ffffff;
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: 0.4pt;
            font-weight: 700;
            padding: 8pt 8pt;
            text-align: left;
            border: 0.6pt solid #0f172a;
        }
        table.data th.r { text-align: right; }
        table.data th.c { text-align: center; }
        table.data td {
            padding: 8pt 8pt;
            border: 0.6pt solid #e5e7eb;
            vertical-align: middle;
            font-size: 9.5pt;
            line-height: 1.35;
        }
        table.data tr.alt td { background: #f8fafc; }
        table.data td.r { text-align: right; white-space: nowrap; }
        table.data td.c { text-align: center; }
        table.data tbody tr { page-break-inside: avoid; break-inside: avoid; }

        /* ---------- Progress bar compact ---------- */
        .progress {
            width: 100%;
            height: 6px;
            background: #e5e7eb;
            border: 0.5pt solid #cbd5e1;
            display: block;
            vertical-align: middle;
        }
        .progress > div { height: 6px; background: #059669; }
        .progress.warn > div { background: #d97706; }

        /* ---------- Badge status (tidak hanya bergantung warna) ---------- */
        .badge {
            display: inline-block;
            padding: 2pt 8pt;
            border-radius: 3pt;
            font-size: 8pt;
            font-weight: 700;
            border: 0.6pt solid transparent;
        }
        .badge.ok { color: #065f46; background: #d1fae5; border-color: #34d399; }
        .badge.no { color: #92400e; background: #fef3c7; border-color: #fbbf24; }

        /* ---------- Keterangan ---------- */
        .note-box {
            border: 0.7pt solid #e5e7eb;
            border-left: 2.5pt solid #0f172a;
            background: #f9fafb;
            padding: 7pt 10pt;
            font-size: 8pt;
            line-height: 1.35;
            color: #4b5563;
            margin-bottom: 12pt;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* ---------- Tanda tangan ---------- */
        table.sig {
            width: 100%;
            margin-top: 30pt;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        table.sig td { width: 50%; text-align: center; font-size: 9pt; color: #374151; border: none; }
        .sig-label { font-size: 8.5pt; color: #6b7280; }
        .sig-line {
            margin-top: 80pt;
            border-top: 1pt solid #111827;
            padding-top: 3pt;
            font-weight: 700;
            color: #111827;
        }

        /* ---------- Kosong ---------- */
        .empty-box {
            border: 1pt dashed #9ca3af;
            padding: 16pt 12pt;
            text-align: center;
            color: #6b7280;
            font-size: 10pt;
        }
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
                    <h1>Laporan Target Capaian</h1>
                    <div class="band-sub">Perbandingan target pemasukan dengan realisasi tahunan</div>
                </td>
            </tr>
        </table>
    </div>
    <div class="accent-bar"></div>

    @php
        $totalTarget = $targets->sum('target_capaian');
        $totalRealisasi = $targets->sum(function ($t) use ($realisasiByTahun) {
            return (float) ($realisasiByTahun[$t->tahun] ?? 0);
        });
        $persenKeseluruhan = $totalTarget > 0 ? round(($totalRealisasi / $totalTarget) * 100) : 0;
        $tahunPertama = $targets->min('tahun');
        $tahunTerakhir = $targets->max('tahun');
    @endphp

    <table class="info">
        <tr>
            <td class="info-cell">
                <div class="info-label">Periode</div>
                <div class="info-val">{{ $tahunPertama && $tahunTerakhir ? $tahunPertama . ' - ' . $tahunTerakhir : 'Belum ada data' }}</div>
            </td>
            <td class="info-cell">
                <div class="info-label">Sumber Realisasi</div>
                <div class="info-val">Total pemasukan tahun berjalan</div>
            </td>
            <td class="info-cell">
                <div class="info-label">Tanggal Cetak</div>
                <div class="info-val">{{ now()->format('d-m-Y H:i') }}</div>
            </td>
            <td class="info-cell">
                <div class="info-label">Jumlah Target</div>
                <div class="info-val">{{ $targets->count() }} tahun</div>
            </td>
        </tr>
    </table>

    @if ($targets->isEmpty())
        <div class="empty-box">Belum ada target capaian yang tercatat.</div>
    @else
        <table class="summary">
            <tr>
                <td>
                    <div class="sum-card navy">
                        <div class="lbl">Total Target</div>
                        <div class="val navy">{{ rupiah($totalTarget) }}</div>
                        <div class="note">Akumulasi target seluruh tahun</div>
                    </div>
                </td>
                <td>
                    <div class="sum-card emerald">
                        <div class="lbl">Total Realisasi</div>
                        <div class="val emerald">{{ rupiah($totalRealisasi) }}</div>
                        <div class="note">Pemasukan yang tercapai</div>
                    </div>
                </td>
                <td>
                    <div class="sum-card {{ $persenKeseluruhan >= 100 ? 'emerald' : 'indigo' }}">
                        <div class="lbl">Capaian Keseluruhan</div>
                        <div class="val {{ $persenKeseluruhan >= 100 ? 'emerald' : 'indigo' }}">{{ $persenKeseluruhan }}%</div>
                        <div class="note">Realisasi &divide; Target</div>
                    </div>
                </td>
                <td>
                    <div class="sum-card navy">
                        <div class="lbl">Jumlah Tahun</div>
                        <div class="val navy">{{ $targets->count() }}</div>
                        <div class="note">Target yang ditetapkan</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="sec-title"><span class="sec-line"></span>Rincian Target per Tahun</div>

        <table class="data">
            <thead>
                <tr>
                    <th style="width:12%">Tahun</th>
                    <th class="r" style="width:23%">Target Capaian</th>
                    <th class="r" style="width:23%">Realisasi</th>
                    <th class="c" style="width:10%">Capaian</th>
                    <th class="c" style="width:20%">Progres</th>
                    <th class="c" style="width:12%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($targets as $index => $target)
                    @php
                        $realisasi = (float) ($realisasiByTahun[$target->tahun] ?? 0);
                        $persen = $target->target_capaian > 0 ? round(($realisasi / $target->target_capaian) * 100) : 0;
                        $tercapai = $persen >= 100;
                        $barWidth = min($persen, 100);
                    @endphp
                    <tr class="{{ $index % 2 === 1 ? 'alt' : '' }}">
                        <td>Tahun {{ $target->tahun }}</td>
                        <td class="r">{{ rupiah($target->target_capaian) }}</td>
                        <td class="r">{{ rupiah($realisasi) }}</td>
                        <td class="c">{{ $persen }}%</td>
                        <td class="c">
                            <span class="progress {{ $tercapai ? '' : 'warn' }}">
                                <div style="width:{{ $barWidth }}%"></div>
                            </span>
                        </td>
                        <td class="c">
                            @if ($tercapai)
                                <span class="badge ok">Tercapai</span>
                            @else
                                <span class="badge no">Belum</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="note-box">
            <b>Keterangan:</b> Capaian dihitung dari total pemasukan pada tahun berjalan dibandingkan dengan target yang ditetapkan.
            Status <b>Tercapai</b> bila realisasi minimal 100% dari target tahunan.
        </div>

        <table class="sig">
            <tr>
                <td>
                    <div class="sig-label">Mengetahui,</div>
                    <div class="sig-label" style="margin-top:2px">Bendahara</div>
                    <div class="sig-line">&nbsp;</div>
                </td>
                <td>
                    <div class="sig-label">Mengetahui,</div>
                    <div class="sig-label" style="margin-top:2px">Kepala / Pimpinan</div>
                    <div class="sig-line">&nbsp;</div>
                </td>
            </tr>
        </table>
    @endif
</body>
</html>
