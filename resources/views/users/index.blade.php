@extends('layouts.app')
@section('topbar-title', 'Manajemen Pengguna')

@section('topbar-actions')
<a href="{{ route('users.create') }}" class="btn btn-primary">+ Tambah Pengguna</a>
@endsection

@section('content')
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Pengguna</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Login Terakhir</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr style="{{ $user->trashed() ? 'opacity:0.5' : '' }}">
                <td>
                    <div style="display:flex;align-items:center;gap:12px">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" style="width:38px;height:38px;border-radius:50%;object-fit:cover;border:1px solid var(--c-border)">
                        @else
                            <div style="width:38px;height:38px;border-radius:50%;background:var(--c-surface);border:1px solid var(--c-border);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;color:var(--c-accent)">
                                {{ $user->initials }}
                            </div>
                        @endif
                        <div>
                            <div style="font-weight:500">{{ $user->name }}</div>
                            @if($user->id === auth()->id())
                            <div style="font-size:0.72rem;color:var(--c-teal)">Anda</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="text-muted" style="font-size:0.875rem">{{ $user->email }}</td>
                <td>
                    <span style="background:{{ $user->role_badge_color }}22;color:{{ $user->role_badge_color }};padding:3px 10px;border-radius:99px;font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px">
                        {{ $user->role }}
                    </span>
                </td>
                <td>
                    @if($user->trashed())
                        <span class="badge badge-fair">Dihapus</span>
                    @elseif($user->is_active)
                        <span class="badge badge-new">Aktif</span>
                    @else
                        <span class="badge badge-fair">Nonaktif</span>
                    @endif
                </td>
                <td style="font-size:0.82rem;color:var(--c-muted)">
                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum pernah' }}
                </td>
                <td>
                    <div style="display:flex;gap:6px;flex-wrap:wrap">
                        @if($user->trashed())
                            <form method="POST" action="{{ route('users.restore', $user->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm">♻️ Pulihkan</button>
                            </form>
                        @else
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-secondary btn-sm">Edit</a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.reset-password', $user) }}" onsubmit="return confirm('Reset password {{ $user->name }}? Password baru akan ditampilkan.')">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm">🔑 Reset PW</button>
                            </form>
                            <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Hapus pengguna {{ $user->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                            @endif
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Role legend -->
<div class="card" style="margin-top:24px">
    <div class="section-title" style="margin-bottom:16px">📋 Keterangan Role</div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px">
        <div style="background:var(--c-surface);border-radius:8px;padding:14px 16px;border:1px solid var(--c-border)">
            <div style="color:#ff6b35;font-weight:700;font-size:0.85rem;margin-bottom:6px">🔴 Admin</div>
            <div style="font-size:0.8rem;color:var(--c-muted);line-height:1.6">Akses penuh: kelola mainan, kategori, pengguna, laporan, dan semua fitur</div>
        </div>
        <div style="background:var(--c-surface);border-radius:8px;padding:14px 16px;border:1px solid var(--c-border)">
            <div style="color:#06d6a0;font-weight:700;font-size:0.85rem;margin-bottom:6px">🟢 Staff</div>
            <div style="font-size:0.8rem;color:var(--c-muted);line-height:1.6">Bisa tambah, edit, hapus mainan & kategori. Tidak bisa kelola pengguna</div>
        </div>
        <div style="background:var(--c-surface);border-radius:8px;padding:14px 16px;border:1px solid var(--c-border)">
            <div style="color:#a7a5c0;font-weight:700;font-size:0.85rem;margin-bottom:6px">⚪ Viewer</div>
            <div style="font-size:0.8rem;color:var(--c-muted);line-height:1.6">Hanya bisa melihat (read-only). Tidak bisa tambah, edit, atau hapus data</div>
        </div>
    </div>
</div>
@endsection
