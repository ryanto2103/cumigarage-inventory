@extends('layouts.app')
@section('topbar-title', 'Kelola Kategori')

@section('content')
<div style="display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start">

    <!-- Category List -->
    <div class="card">
        <div class="section-title" style="margin-bottom:20px">📦 Daftar Kategori</div>
        @if($categories->count())
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Warna</th>
                        <th>Nama</th>
                        <th>Jumlah Mainan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($categories as $cat)
                <tr>
                    <td>
                        <span style="display:inline-block;width:24px;height:24px;border-radius:6px;background:{{ $cat->color }};border:1px solid rgba(255,255,255,0.1)"></span>
                    </td>
                    <td style="font-weight:500">{{ $cat->name }}</td>
                    <td>
                        <a href="{{ route('toys.index', ['category' => $cat->id]) }}" style="color:var(--c-accent);text-decoration:none">
                            {{ $cat->toys_count }} mainan
                        </a>
                    </td>
                    <td>
                        <div style="display:flex;gap:8px">
                            <!-- Edit inline modal trigger -->
                            <button class="btn btn-secondary btn-sm" onclick="openEdit({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ $cat->color }}')">Edit</button>
                            <form method="POST" action="{{ route('categories.destroy', $cat) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-muted" style="text-align:center;padding:40px;font-size:0.875rem">Belum ada kategori. Tambahkan di sebelah kanan.</div>
        @endif
    </div>

    <!-- Add / Edit Form -->
    <div class="card" id="cat-form-card">
        <div class="section-title" style="margin-bottom:20px" id="cat-form-title">➕ Tambah Kategori</div>
        <form method="POST" id="cat-form" action="{{ route('categories.store') }}">
            @csrf
            <span id="cat-method-field"></span>
            <div class="form-group">
                <label class="form-label">Nama Kategori</label>
                <input type="text" name="name" id="cat-name" class="form-control" required placeholder="Contoh: Action Figure, Puzzle..." value="{{ old('name') }}">
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Warna Label</label>
                <div style="display:flex;gap:10px;align-items:center">
                    <input type="color" name="color" id="cat-color" value="{{ old('color', '#ff6b35') }}" style="width:44px;height:44px;border:none;border-radius:8px;cursor:pointer;background:transparent;padding:0">
                    <input type="text" id="cat-color-text" class="form-control font-mono" value="{{ old('color', '#ff6b35') }}" placeholder="#ff6b35" style="max-width:120px">
                </div>
            </div>
            <div style="display:flex;gap:10px">
                <button type="submit" class="btn btn-primary" id="cat-submit-btn">Simpan</button>
                <button type="button" class="btn btn-secondary" id="cat-cancel-btn" style="display:none" onclick="resetForm()">Batal</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const colorPicker = document.getElementById('cat-color');
const colorText   = document.getElementById('cat-color-text');
colorPicker.addEventListener('input', () => colorText.value = colorPicker.value);
colorText.addEventListener('input', () => { if (/^#[0-9A-Fa-f]{6}$/.test(colorText.value)) colorPicker.value = colorText.value; });

function openEdit(id, name, color) {
    document.getElementById('cat-form-title').textContent = '✏️ Edit Kategori';
    document.getElementById('cat-name').value  = name;
    colorPicker.value = color;
    colorText.value   = color;
    document.getElementById('cat-form').action = '/categories/' + id;
    document.getElementById('cat-method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('cat-submit-btn').textContent = '💾 Simpan Perubahan';
    document.getElementById('cat-cancel-btn').style.display = 'inline-flex';
    document.getElementById('cat-form-card').scrollIntoView({ behavior: 'smooth' });
}

function resetForm() {
    document.getElementById('cat-form-title').textContent = '➕ Tambah Kategori';
    document.getElementById('cat-name').value = '';
    colorPicker.value = '#ff6b35';
    colorText.value   = '#ff6b35';
    document.getElementById('cat-form').action = '{{ route("categories.store") }}';
    document.getElementById('cat-method-field').innerHTML = '';
    document.getElementById('cat-submit-btn').textContent = 'Simpan';
    document.getElementById('cat-cancel-btn').style.display = 'none';
}
</script>
@endpush
@endsection
