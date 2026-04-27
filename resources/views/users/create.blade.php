@extends('layouts.app')
@section('topbar-title', isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna')

@section('content')
<div style="max-width:560px">
    <form method="POST" action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}">
        @csrf
        @if(isset($user)) @method('PUT') @endif

        <div class="card">
            <div class="section-title" style="margin-bottom:22px">
                {{ isset($user) ? '✏️ Edit Pengguna' : '👤 Tambah Pengguna Baru' }}
            </div>

            <div class="form-group">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required placeholder="Nama lengkap pengguna">
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required placeholder="email@contoh.com">
                @error('email')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Password {{ isset($user) ? '(kosongkan jika tidak diubah)' : '*' }}</label>
                    <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter" {{ isset($user) ? '' : 'required' }} autocomplete="new-password">
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" autocomplete="new-password">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Role *</label>
                <select name="role" class="form-control" required>
                    <option value="admin"  {{ old('role', $user->role ?? '') == 'admin'  ? 'selected' : '' }}>🔴 Admin — Akses penuh</option>
                    <option value="staff"  {{ old('role', $user->role ?? 'staff') == 'staff'  ? 'selected' : '' }}>🟢 Staff — Edit mainan & kategori</option>
                    <option value="viewer" {{ old('role', $user->role ?? '') == 'viewer' ? 'selected' : '' }}>⚪ Viewer — Read only</option>
                </select>
                @error('role')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
                    <input type="checkbox" name="is_active" value="1"
                        {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}
                        style="width:17px;height:17px;accent-color:var(--c-accent)">
                    <span style="font-size:0.875rem">Akun aktif (pengguna dapat login)</span>
                </label>
            </div>

            <!-- Role description -->
            <div id="role-desc" style="background:var(--c-surface);border-radius:8px;padding:12px 14px;font-size:0.8rem;color:var(--c-muted);border:1px solid var(--c-border);margin-bottom:4px"></div>
        </div>

        <div style="display:flex;gap:12px;margin-top:20px">
            <button type="submit" class="btn btn-primary">
                {{ isset($user) ? '💾 Simpan Perubahan' : '➕ Tambah Pengguna' }}
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
const roleDesc = {
    admin:  '🔴 Admin memiliki akses penuh: mengelola mainan, kategori, pengguna, dan laporan.',
    staff:  '🟢 Staff dapat menambah, mengedit, dan menghapus mainan & kategori, namun tidak bisa mengelola pengguna.',
    viewer: '⚪ Viewer hanya bisa melihat data (read-only). Tidak bisa mengubah apapun.',
};
const sel = document.querySelector('[name="role"]');
const desc = document.getElementById('role-desc');
function updateDesc() { desc.textContent = roleDesc[sel.value] || ''; }
sel.addEventListener('change', updateDesc);
updateDesc();
</script>
@endpush
@endsection
