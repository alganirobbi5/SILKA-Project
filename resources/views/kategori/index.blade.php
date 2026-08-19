@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
    <div class="page-header">
        <div>
            <div class="page-kicker">Master Kategori</div>
            <h2 class="page-title">Daftar Kategori</h2>
            <p class="page-sub">Kelola kategori transaksi SILKA.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('kategori.create') }}" class="btn btn-primary">
                @include('partials.icon', ['name' => 'plus', 'size' => 16]) Tambah Kategori
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Kategori</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kategoris as $kategori)
                            <tr>
                                <td><span class="badge badge-neutral">#{{ $kategori->id }}</span></td>
                                <td class="cell-main">{{ $kategori->kategori }}
                                    @if ($kategori->id === 1)
                                        <span class="badge badge-masuk">Default</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="action-cell">
                                        <a href="{{ route('kategori.edit', $kategori->id) }}" class="action-btn">
                                            @include('partials.icon', ['name' => 'edit', 'size' => 14]) Edit
                                        </a>
                                        @if ($kategori->id !== 1)
                                            <form method="POST" action="{{ route('kategori.destroy', $kategori->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini? Transaksi terkait akan dipindahkan ke kategori default.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn danger">
                                                    @include('partials.icon', ['name' => 'trash', 'size' => 14]) Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge badge-amber">Tidak dapat dihapus</span>
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <div class="empty-state">
                                        <div class="empty-icon">@include('partials.icon', ['name' => 'kategori', 'size' => 28])</div>
                                        <p>Belum ada kategori.</p>
                                        <a href="{{ route('kategori.create') }}" class="btn btn-primary">Tambah Kategori</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $kategoris->links() }}
        </div>
    </div>
@endsection