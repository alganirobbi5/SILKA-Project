@extends('layouts.app')

@section('title', 'User')

@section('content')
    <div class="page-header">
        <div>
            <div class="page-kicker">Manajemen Pengguna</div>
            <h2 class="page-title">Daftar User</h2>
            <p class="page-sub">Kelola pengguna yang dapat mengakses aplikasi.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                @include('partials.icon', ['name' => 'plus', 'size' => 16]) Tambah User
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Level</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            @php
                                $initials = collect(explode(' ', $user->name))
                                    ->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                            @endphp
                            <tr>
                                <td>
                                    @if ($user->foto)
                                        <img src="{{ $user->foto_url }}" alt="Foto {{ $user->name }}" style="width:42px;height:42px;object-fit:cover;border-radius:50%;border:2px solid var(--border)">
                                    @else
                                        <span class="avatar">{{ $initials }}</span>
                                    @endif
                                </td>
                                <td class="cell-main">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if ($user->isAdmin())
                                        <span class="badge badge-brand">@include('partials.icon', ['name' => 'user', 'size' => 12]) Admin</span>
                                    @else
                                        <span class="badge badge-neutral">@include('partials.icon', ['name' => 'user', 'size' => 12]) Bendahara</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="action-cell">
                                        <a href="{{ route('users.edit', $user->id) }}" class="action-btn">
                                            @include('partials.icon', ['name' => 'edit', 'size' => 14]) Edit
                                        </a>
                                        @if ($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('users.destroy', $user->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn danger">
                                                    @include('partials.icon', ['name' => 'trash', 'size' => 14]) Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge badge-amber">Akun sendiri</span>
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <div class="empty-icon">@include('partials.icon', ['name' => 'users', 'size' => 28])</div>
                                        <p>Belum ada user.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $users->links() }}
        </div>
    </div>
@endsection