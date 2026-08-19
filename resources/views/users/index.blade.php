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
                            <th>Profil</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Level</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    <span class="avatar">
                                        @include('partials.icon', ['name' => 'user', 'size' => 18])
                                    </span>
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