@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="dash-bg" aria-hidden="true">
        <div class="blob b1"></div>
        <div class="blob b2"></div>
        <div class="blob b3"></div>
        @for ($i = 0; $i < 9; $i++)
            <div class="particle {{ $i % 3 == 0 ? 'p2' : ($i % 3 == 1 ? 'p3' : '') }}"
                 style="left: {{ 4 + $i * 11 }}%; --dx: {{ ($i % 2 == 0 ? 1 : -1) * (12 + $i * 4) }}px; animation-duration: {{ 9 + $i * 2 }}s; animation-delay: {{ $i * 1.4 }}s;"></div>
        @endfor
    </div>

    <div class="page-header">
        <div>
            <div class="page-kicker">Ringkasan Keuangan</div>
            <h2 class="page-title">Dashboard</h2>
            <p class="page-sub">Halo {{ auth()->user()->name }}, ini ringkasan keuangan periode {{ $year }}.</p>
        </div>
        <div class="page-actions">
            <nav class="year-pills" aria-label="Pilih Tahun">
                @foreach ($tahunTersedia as $tahun)
                    <a href="{{ route('dashboard', ['year' => $tahun]) }}"
                       class="{{ $tahun == $year ? 'active' : '' }}">
                        {{ $tahun }}
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    @if (!$selectedHasData)
        <div class="dash-notice" id="dashNotice">
            @include('partials.icon', ['name' => 'info', 'size' => 18])
            <span>
                <strong>Belum ada transaksi di tahun {{ $year }}.</strong>
                @if ($latestDataYear && $latestDataYear != $year)
                    Data terakhir tersedia di tahun {{ $latestDataYear }}.
                @else
                    Mulai catat transaksi pertama Anda.
                @endif
            </span>
            @if ($latestDataYear && $latestDataYear != $year)
                <a href="{{ route('dashboard', ['year' => $latestDataYear]) }}" class="btn btn-primary btn-sm"
                   style="margin-left:auto">
                    @include('partials.icon', ['name' => 'refresh', 'size' => 14]) Lihat data {{ $latestDataYear }}
                </a>
            @endif
            <button type="button" class="btn btn-secondary btn-sm" data-close aria-label="Tutup">
                @include('partials.icon', ['name' => 'x', 'size' => 14])
            </button>
        </div>
    @endif

    <div class="stat-grid">
        <div class="stat-card tilt reveal" style="--tile-grad:linear-gradient(135deg,#10b981,#059669);--d:.05s">
            <div class="stat-top">
                <span class="stat-icon">@include('partials.icon', ['name' => 'trend-up', 'size' => 22])</span>
                <span class="stat-trend up">@include('partials.icon', ['name' => 'trend-up', 'size' => 12]) Hari Ini</span>
            </div>
            <div>
                <div class="stat-label">Pemasukan Hari Ini</div>
                <div class="stat-value green" data-count data-target="{{ $hariIni->pemasukan ?? 0 }}" data-prefix="Rp">{{ rupiah($hariIni->pemasukan ?? 0) }}</div>
            </div>
        </div>

        <div class="stat-card tilt reveal" style="--tile-grad:linear-gradient(135deg,#f43f5e,#e11d48);--d:.1s">
            <div class="stat-top">
                <span class="stat-icon">@include('partials.icon', ['name' => 'trend-down', 'size' => 22])</span>
                <span class="stat-trend down">@include('partials.icon', ['name' => 'trend-down', 'size' => 12]) Hari Ini</span>
            </div>
            <div>
                <div class="stat-label">Pengeluaran Hari Ini</div>
                <div class="stat-value red" data-count data-target="{{ $hariIni->pengeluaran ?? 0 }}" data-prefix="Rp">{{ rupiah($hariIni->pengeluaran ?? 0) }}</div>
            </div>
        </div>

        <div class="stat-card tilt reveal" style="--tile-grad:linear-gradient(135deg,#0ea5e9,#0284c7);--d:.15s">
            <div class="stat-top">
                <span class="stat-icon">@include('partials.icon', ['name' => 'wallet', 'size' => 22])</span>
                <span class="stat-trend up">@include('partials.icon', ['name' => 'trend-up', 'size' => 12]) Bulan Ini</span>
            </div>
            <div>
                <div class="stat-label">Pemasukan Bulan Ini</div>
                <div class="stat-value green" data-count data-target="{{ $bulanIni->pemasukan ?? 0 }}" data-prefix="Rp">{{ rupiah($bulanIni->pemasukan ?? 0) }}</div>
            </div>
        </div>

        <div class="stat-card tilt reveal" style="--tile-grad:linear-gradient(135deg,#f59e0b,#d97706);--d:.2s">
            <div class="stat-top">
                <span class="stat-icon">@include('partials.icon', ['name' => 'transaksi', 'size' => 22])</span>
                <span class="stat-trend down">@include('partials.icon', ['name' => 'trend-down', 'size' => 12]) Bulan Ini</span>
            </div>
            <div>
                <div class="stat-label">Pengeluaran Bulan Ini</div>
                <div class="stat-value red" data-count data-target="{{ $bulanIni->pengeluaran ?? 0 }}" data-prefix="Rp">{{ rupiah($bulanIni->pengeluaran ?? 0) }}</div>
            </div>
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card tilt reveal" style="--tile-grad:linear-gradient(135deg,#10b981,#059669);--d:.25s">
            <div class="stat-top">
                <span class="stat-icon">@include('partials.icon', ['name' => 'trend-up', 'size' => 22])</span>
                <span class="stat-trend flat">Tahun {{ $year }}</span>
            </div>
            <div>
                <div class="stat-label">Pemasukan Tahun {{ $year }}</div>
                <div class="stat-value green" data-count data-target="{{ $tahunMasuk }}" data-prefix="Rp">{{ rupiah($tahunMasuk) }}</div>
            </div>
        </div>

        <div class="stat-card tilt reveal" style="--tile-grad:linear-gradient(135deg,#f43f5e,#e11d48);--d:.3s">
            <div class="stat-top">
                <span class="stat-icon">@include('partials.icon', ['name' => 'trend-down', 'size' => 22])</span>
                <span class="stat-trend flat">Tahun {{ $year }}</span>
            </div>
            <div>
                <div class="stat-label">Pengeluaran Tahun {{ $year }}</div>
                <div class="stat-value red" data-count data-target="{{ $tahunKeluar }}" data-prefix="Rp">{{ rupiah($tahunKeluar) }}</div>
            </div>
        </div>

        <div class="stat-card tilt reveal" style="--tile-grad:linear-gradient(135deg,#6366f1,#4f46e5);--d:.35s">
            <div class="stat-top">
                <span class="stat-icon">@include('partials.icon', ['name' => 'target', 'size' => 22])</span>
                <span class="stat-trend flat">Target {{ $year }}</span>
            </div>
            <div>
                <div class="stat-label">Target Capaian {{ $year }}</div>
                @if ($target)
                    <div class="stat-value" data-count data-target="{{ $targetNominal }}" data-prefix="Rp">{{ rupiah($targetNominal) }}</div>
                    @if ($targetPersen !== null)
                        <div style="margin-top:12px">
                            <div class="progress-track">
                                <div class="progress-fill {{ $targetPersen >= 100 ? 'high' : '' }}" data-width="{{ $targetPersen }}" style="width:0%"></div>
                            </div>
                            <div class="stat-hint">Capaian {{ $targetPersen }}% dari target</div>
                        </div>
                    @endif
                @else
                    <div class="stat-value" style="font-size:19px">Belum ditentukan</div>
                    <div class="stat-hint">Set target lewat menu Target Capaian.</div>
                @endif
            </div>
        </div>

        <div class="stat-card tilt reveal" style="--tile-grad:linear-gradient(135deg,#0ea5e9,#0284c7);--d:.4s">
            <div class="stat-top">
                <span class="stat-icon">@include('partials.icon', ['name' => 'scale', 'size' => 22])</span>
                <span class="stat-trend flat">Tahun {{ $year - 1 }}</span>
            </div>
            <div>
                <div class="stat-label">Piutang / Hutang {{ $year - 1 }}</div>
                <div class="stat-value" style="font-size:19px">Piutang {{ rupiah($piutang) }}</div>
                <div class="stat-hint">Hutang {{ rupiah($hutang) }}</div>
            </div>
        </div>
    </div>

    <div class="card reveal" style="--d:.45s">
        <div class="card-header">
            <span>Komposisi Keuangan Tahun {{ $year }}</span>
        </div>
        <div class="card-body">
            <div class="donut-card">
                <div class="donut" style="--p1:0%" data-p1="{{ $persenPemasukan }}">
                    <div class="donut-center">
                        <strong data-count data-target="{{ $tahunMasuk + $tahunKeluar }}" data-prefix="Rp">{{ rupiah($tahunMasuk + $tahunKeluar) }}</strong>
                        <span>Total Tahun {{ $year }}</span>
                    </div>
                </div>
                <div class="legend" style="flex:1;min-width:220px">
                    <div class="legend-item">
                        <span class="legend-dot" style="background:var(--income-1)"></span>
                        <span class="legend-label">Pemasukan</span>
                        <span class="legend-value" style="color:var(--income-2)">{{ rupiah($tahunMasuk) }} ({{ $persenPemasukan }}%)</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot" style="background:var(--expense-1)"></span>
                        <span class="legend-label">Pengeluaran</span>
                        <span class="legend-value" style="color:var(--expense-1)">{{ rupiah($tahunKeluar) }} ({{ $persenPengeluaran }}%)</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot" style="background:var(--brand-1)"></span>
                        <span class="legend-label">Saldo Bersih</span>
                        <span class="legend-value" style="color:var(--brand-2)">{{ rupiah($tahunMasuk - $tahunKeluar) }}</span>
                    </div>
                </div>
            </div>

            @php
                $bulanNama = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            @endphp
            <div class="section-title" style="margin-top:26px">
                @include('partials.icon', ['name' => 'laporan', 'size' => 16]) Tren Pemasukan & Pengeluaran {{ $year }}
            </div>
            <div class="mini-bars">
                @for ($i = 1; $i <= 12; $i++)
                    <div class="bar-wrap reveal" style="--d: {{ ($i - 1) * 0.03 }}s">
                        <div class="bar inc" style="height: {{ $maxTren > 0 ? round($bulanTren[$i]['masuk'] / $maxTren * 100) : 0 }}%"></div>
                        <div class="bar exp" style="height: {{ $maxTren > 0 ? round($bulanTren[$i]['keluar'] / $maxTren * 100) : 0 }}%"></div>
                        <span class="bar-label">{{ $bulanNama[$i - 1] }}</span>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:22px">
        <div class="card reveal" style="margin-bottom:0;--d:.5s">
            <div class="card-header">
                <span>Transaksi Terbaru</span>
                <a href="{{ route('transaksi.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
            </div>
            <div class="card-body" style="padding:6px 22px">
                <ul class="recent-list">
                    @forelse ($recentTransaksis as $t)
                        <li>
                            <span class="recent-avatar {{ $t->jenis === 'pemasukan' ? 'inc' : 'exp' }}">
                                @include('partials.icon', ['name' => $t->jenis === 'pemasukan' ? 'trend-up' : 'trend-down', 'size' => 20])
                            </span>
                            <div class="recent-main">
                                <div class="recent-title">{{ $t->keterangan ?: ($t->kategori->kategori ?? 'Tanpa keterangan') }}</div>
                                <div class="recent-sub">{{ $t->tanggal->format('d M Y') }} &middot; {{ $t->coa->nama_coa ?? '-' }}</div>
                            </div>
                            <div class="recent-value {{ $t->jenis === 'pemasukan' ? 'inc' : 'exp' }}">
                                {{ $t->jenis === 'pemasukan' ? '+' : '-' }}{{ rupiah($t->nominal) }}
                            </div>
                        </li>
                    @empty
                        <li>
                            <div class="empty-state" style="padding:32px 24px">
                                <div class="empty-icon">@include('partials.icon', ['name' => 'inbox', 'size' => 28])</div>
                                <p>Belum ada transaksi</p>
                                <small>Mulai catat transaksi pertama Anda.</small>
                            </div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="card reveal" style="margin-bottom:0;--d:.55s">
            <div class="card-header">
                <span>Aksi Cepat</span>
            </div>
            <div class="card-body">
                <div class="quick-grid">
                    <a href="{{ route('transaksi.create') }}" class="quick-card">
                        @include('partials.icon', ['name' => 'plus', 'size' => 22])
                        Tambah Transaksi
                    </a>
                    <a href="{{ route('coa.index') }}" class="quick-card">
                        @include('partials.icon', ['name' => 'coa', 'size' => 22])
                        Kelola COA
                    </a>
                    <a href="{{ route('laporan.index') }}" class="quick-card">
                        @include('partials.icon', ['name' => 'laporan', 'size' => 22])
                        Cetak Laporan
                    </a>
                    <a href="{{ route('target-capaians.create') }}" class="quick-card">
                        @include('partials.icon', ['name' => 'target', 'size' => 22])
                        Set Target
                    </a>
                    @can('manage-users')
                        <a href="{{ route('users.create') }}" class="quick-card">
                            @include('partials.icon', ['name' => 'users', 'size' => 22])
                            Tambah User
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var isTouch = ('ontouchstart' in window) || navigator.maxTouchPoints > 0;
            var supportsRegister = !!(window.CSS && CSS.registerProperty);

            // Reveal bertahap
            var revealEls = document.querySelectorAll('.reveal');
            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (e) {
                        if (e.isIntersecting) {
                            e.target.classList.add('is-visible');
                            io.unobserve(e.target);
                        }
                    });
                }, { threshold: 0.12 });
                revealEls.forEach(function (el) { io.observe(el); });
            } else {
                revealEls.forEach(function (el) { el.classList.add('is-visible'); });
            }

            // Format angka seperti PHP number_format(v, 2, ',', '.')
            function formatRp(value) {
                var v = Number(value);
                var parts = v.toFixed(2).split('.');
                var intPart = parts[0];
                var neg = intPart.charAt(0) === '-';
                intPart = neg ? intPart.slice(1) : intPart;
                intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                return (neg ? '-' : '') + intPart + ',' + parts[1];
            }

            function animateCount(el) {
                var target = parseFloat(el.getAttribute('data-target')) || 0;
                var prefix = el.getAttribute('data-prefix') || '';
                var dur = 900;
                var start = performance.now();
                function tick(now) {
                    var p = Math.min((now - start) / dur, 1);
                    var eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = prefix + formatRp(target * eased);
                    if (p < 1) requestAnimationFrame(tick);
                    else el.textContent = prefix + formatRp(target);
                }
                requestAnimationFrame(tick);
            }

            var countEls = document.querySelectorAll('[data-count]');
            if (reduceMotion) {
                countEls.forEach(function (el) {
                    el.textContent = (el.getAttribute('data-prefix') || '') + formatRp(el.getAttribute('data-target'));
                });
            } else if ('IntersectionObserver' in window) {
                var io2 = new IntersectionObserver(function (entries) {
                    entries.forEach(function (e) {
                        if (e.isIntersecting) {
                            animateCount(e.target);
                            io2.unobserve(e.target);
                        }
                    });
                }, { threshold: 0.15 });
                countEls.forEach(function (el) { io2.observe(el); });
            } else {
                countEls.forEach(animateCount);
            }

            // Donut: animasikan --p1 dari 0 ke target
            var donut = document.querySelector('.donut[data-p1]');
            if (donut) {
                var targetP1 = parseFloat(donut.getAttribute('data-p1')) || 50;
                donut.classList.add('is-live');
                if (reduceMotion || !supportsRegister) {
                    donut.style.setProperty('--p1', targetP1 + '%');
                } else {
                    requestAnimationFrame(function () {
                        requestAnimationFrame(function () {
                            donut.style.setProperty('--p1', targetP1 + '%');
                        });
                    });
                }
            }

            // Progress bar capaian target
            document.querySelectorAll('.progress-fill[data-width]').forEach(function (bar) {
                var w = parseFloat(bar.getAttribute('data-width')) || 0;
                if (reduceMotion) { bar.style.width = w + '%'; return; }
                requestAnimationFrame(function () {
                    requestAnimationFrame(function () { bar.style.width = w + '%'; });
                });
            });

            // Tilt 3D kartu statistik
            if (!reduceMotion && !isTouch) {
                document.querySelectorAll('.tilt').forEach(function (card) {
                    var raf = null;
                    card.addEventListener('mousemove', function (e) {
                        var r = card.getBoundingClientRect();
                        var x = (e.clientX - r.left) / r.width - .5;
                        var y = (e.clientY - r.top) / r.height - .5;
                        if (raf) cancelAnimationFrame(raf);
                        raf = requestAnimationFrame(function () {
                            card.style.transform = 'perspective(900px) rotateY(' + (x * 6) + 'deg) rotateX(' + (-y * 6) + 'deg) translateY(-3px)';
                        });
                    });
                    card.addEventListener('mouseleave', function () {
                        if (raf) cancelAnimationFrame(raf);
                        card.style.transform = '';
                    });
                });
            }

            // Transisi geser saat ganti tahun
            document.querySelectorAll('.year-pills a').forEach(function (a) {
                a.addEventListener('click', function (e) {
                    if (reduceMotion || a.classList.contains('active')) return;
                    e.preventDefault();
                    var shell = document.querySelector('.app-shell');
                    if (shell) shell.classList.add('page-exit');
                    setTimeout(function () { window.location.href = a.href; }, 240);
                });
            });

            // Tutup notifikasi data kosong
            var notice = document.getElementById('dashNotice');
            if (notice) {
                var closeBtn = notice.querySelector('[data-close]');
                if (closeBtn) {
                    closeBtn.addEventListener('click', function () { notice.remove(); });
                }
            }
        })();
    </script>
@endpush