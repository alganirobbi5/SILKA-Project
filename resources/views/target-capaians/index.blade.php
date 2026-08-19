@extends('layouts.app')

@section('title', 'Target Capaian')

@section('content')
    <div class="page-header">
        <div>
            <div class="page-kicker">Perencanaan</div>
            <h2 class="page-title">Target Capaian Tahunan</h2>
            <p class="page-sub">Tentukan target pemasukan yang ingin dicapai setiap tahun.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('target-capaians.pdf') }}" class="btn btn-primary">
                @include('partials.icon', ['name' => 'download', 'size' => 16]) Cetak PDF
            </a>
            <a href="{{ route('target-capaians.create') }}" class="btn btn-primary">
                @include('partials.icon', ['name' => 'plus', 'size' => 16]) Tambah Target
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tahun</th>
                            <th class="text-right">Target Capaian</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($targets as $target)
                            <tr>
                                <td><span class="chip">@include('partials.icon', ['name' => 'calendar', 'size' => 14]) Tahun {{ $target->tahun }}</span></td>
                                <td class="nominal pos">{{ rupiah($target->target_capaian) }}</td>
                                <td class="text-center">
                                    <span class="action-cell">
                                        <a href="{{ route('target-capaians.edit', $target->id) }}" class="action-btn">
                                            @include('partials.icon', ['name' => 'edit', 'size' => 14]) Edit
                                        </a>
                                        <form method="POST" action="{{ route('target-capaians.destroy', $target->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus target ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn danger">
                                                @include('partials.icon', ['name' => 'trash', 'size' => 14]) Hapus
                                            </button>
                                        </form>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <div class="empty-state">
                                        <div class="empty-icon">@include('partials.icon', ['name' => 'target', 'size' => 28])</div>
                                        <p>Belum ada target capaian.</p>
                                        <a href="{{ route('target-capaians.create') }}" class="btn btn-primary">Tambah Target</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $targets->links() }}
        </div>
    </div>
@endsection