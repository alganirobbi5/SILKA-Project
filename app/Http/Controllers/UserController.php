<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(20);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->safe()->except(['foto']);
        $data['password'] = Hash::make($data['password']);

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $nama = uniqid('foto_', true) . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('foto', $nama, 'public');
            $data['foto'] = $nama;
        }

        $user = User::create($data);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->safe()->except(['foto', 'password']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $nama = uniqid('foto_', true) . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('foto', $nama, 'public');
            $data['foto'] = $nama;

            if ($user->foto && Storage::disk('public')->exists('foto/' . $user->foto)) {
                Storage::disk('public')->delete('foto/' . $user->foto);
            }
        }

        try {
            $user->update($data);
        } catch (\Exception $e) {
            if (isset($nama) && $nama && Storage::disk('public')->exists('foto/' . $nama)) {
                Storage::disk('public')->delete('foto/' . $nama);
            }
            throw $e;
        }

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun yang sedang digunakan.');
        }

        $adminCount = User::where('level', User::LEVEL_ADMIN)->count();
        if ($user->isAdmin() && $adminCount <= 1) {
            return back()->with('error', 'Administrator terakhir tidak dapat dihapus.');
        }

        try {
            $foto = $user->foto;
            $user->delete();

            if ($foto && Storage::disk('public')->exists('foto/' . $foto)) {
                Storage::disk('public')->delete('foto/' . $foto);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
