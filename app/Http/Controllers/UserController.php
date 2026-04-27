<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserController extends Controller
{
    private function authorizeAdmin(): void
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Hanya administrator yang dapat mengelola pengguna.');
        }
    }

    public function index()
    {
        $this->authorizeAdmin();
        $users = User::withTrashed()->orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        return view('users.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:admin,staff,viewer',
            'is_active'=> 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $user = User::create($validated);

        return redirect()->route('users.index')
            ->with('success', "Pengguna \"{$user->name}\" berhasil ditambahkan!");
    }

    public function edit(User $user)
    {
        $this->authorizeAdmin();
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeAdmin();
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role'     => 'required|in:admin,staff,viewer',
            'is_active'=> 'boolean',
        ]);

        // Prevent self-demotion
        if ($user->id === Auth::id() && $validated['role'] !== 'admin') {
            return back()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri.');
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', "Pengguna \"{$user->name}\" berhasil diperbarui!");
    }

    public function destroy(User $user)
    {
        $this->authorizeAdmin();
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        $user->delete();
        return back()->with('success', "Pengguna \"{$user->name}\" berhasil dihapus.");
    }

    public function restore($id)
    {
        $this->authorizeAdmin();
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return back()->with('success', "Pengguna \"{$user->name}\" berhasil dipulihkan.");
    }

    public function resetPassword(User $user)
    {
        $this->authorizeAdmin();
        $newPassword = Str::random(10);
        $user->update(['password' => $newPassword]);

        return back()->with('success', "Password baru untuk {$user->name}: <strong>{$newPassword}</strong>");
    }
}
