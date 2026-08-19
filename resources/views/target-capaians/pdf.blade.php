<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Target Capaian - SILKA Keuangan</title>
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

    <div class="chips">
        <span class="chip">Periode: {{ $tahunPertama && $tahunTerakhir ? $tahunPertama . ' - ' . $tahunTerakhir : 'Belum ada data' }}</span>
        <span class="chip">Sumber realisasi: Total pemasukan tahun berjalan</span>
    </div>

    <div class="meta">Dicetak pada {{ now()->format('d-m-Y H:i') }} &nbsp;&bull;&nbsp; {{ $targets->count() }} target tercatat</div>

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
                    <th>Tahun</th>
                    <th class="r">Target Capaian</th>
                    <th class="r">Realisasi</th>
                    <th class="r">Capaian</th>
                    <th class="c">Progres</th>
                    <th class="c">Status</th>
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
                        <td class="r">{{ $persen }}%</td>
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