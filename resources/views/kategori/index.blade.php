@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
    <div class="card">
        <div class="card-header">
            <span>Daftar Kategori</span>
            <a href="{{ route('kategori.create') }}" class="btn btn-primary">Tambah Kategori</a>
        </div>
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
                                <td>{{ $kategori->id }}</td>
                                <td>{{ $kategori->kategori }}
                                    @if ($kategori->id === 1)
                                        <span class="badge badge-masuk">Default</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('kategori.edit', $kategori->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                                    @if ($kategori->id !== 1)
                                        <form method="POST" action="{{ route('kategori.destroy', $kategori->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini? Transaksi terkait akan dipindahkan ke kategori default.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    @else
                                        <span class="badge">Tidak dapat dihapus</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <div class="empty-state">
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
