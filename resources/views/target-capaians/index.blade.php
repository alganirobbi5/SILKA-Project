@extends('layouts.app')

@section('title', 'Target Capaian')

@section('content')
    <div class="card">
        <div class="card-header">
            <span>Target Capaian Tahunan</span>
            <a href="{{ route('target-capaians.create') }}" class="btn btn-primary">Tambah Target</a>
        </div>
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
                                <td>{{ $target->tahun }}</td>
                                <td class="nominal">{{ rupiah($target->target_capaian) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('target-capaians.edit', $target->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('target-capaians.destroy', $target->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus target ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <div class="empty-state">
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