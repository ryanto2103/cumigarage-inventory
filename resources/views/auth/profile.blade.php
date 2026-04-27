@extends('layouts.app')
@section('topbar-title', 'Profil Saya')

@section('content')
<div style="max-width:640px">
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <!-- Avatar + Info -->
        <div class="card" style="margin-bottom:20px">
            <div class="section-title" style="margin-bottom:20px">👤 Informasi Akun</div>

            <div style="display:flex;align-items:center;gap:20px;margin-bottom:24px">
                <!-- Avatar preview -->
                <div style="position:relative">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" id="avatar-preview"
                             style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:2px solid var(--c-border)">
                    @else
                        <div id="avatar-initials"
                             style="width:80px;height:80px;border-radius:50%;background:var(--c-surface);border:2px solid var(--c-border);display:flex;align-items:center;justify-content:center;font-family:var(--font-head);font-size:1.6rem;font-weight:700;color:var(--c-accent)">
                             {{ $user->initials }}
                        </div>
                        <img src="" id="avatar-preview" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:2px solid var(--c-border);display:none">
                    @endif
                    <label for="avatar-input" style="position:absolute;bottom:-4px;right:-4px;background:var(--c-accent);border-radius:50%;width:26px;height:26px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:0.75rem">📷</label>
                    <input type="file" id="avatar-input" name="avatar" accept="image/*" style="display:none">
                </div>
                <div>
                    <div style="font-size:1.1rem;font-weight:600">{{ $user->name }}</div>
                    <div class="text-muted" style="font-size:0.85rem">{{ $user->email }}</div>
                    <div style="margin-top:6px">
                        <span style="background:rgba(255,107,53,0.15);color:var(--c-accent);padding:3px 10px;border-radius:99px;font-size:0.75rem;font-weight:600">
                            {{ $user->role_label }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="font-size:0.8rem;color:var(--c-muted);background:var(--c-surface);border-radius:8px;padding:10px 14px">
                Login terakhir: {{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : 'Belum pernah login' }}
            </div>
        </div>

        <!-- Change Password -->
        <div class="card" style="margin-bottom:20px">
            <div class="section-title" style="margin-bottom:4px">🔒 Ganti Password</div>
            <div class="text-muted" style="font-size:0.82rem;margin-bottom:18px">Kosongkan jika tidak ingin mengubah password</div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter" autocomplete="new-password">
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru" autocomplete="new-password">
                </div>
            </div>
        </div>

        <div style="display:flex;gap:12px">
            <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.getElementById('avatar-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = ev => {
        const preview = document.getElementById('avatar-preview');
        const initials = document.getElementById('avatar-initials');
        preview.src = ev.target.result;
        preview.style.display = 'block';
        if (initials) initials.style.display = 'none';
    };
    reader.readAsDataURL(file);
});
</script>
@endpush
@endsection
