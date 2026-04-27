@extends('layouts.app')
@section('topbar-title', isset($toy) ? 'Edit Mainan' : 'Tambah Mainan')

@section('content')
<div style="max-width:900px">
    <form method="POST" action="{{ isset($toy) ? route('toys.update', $toy) : route('toys.store') }}" enctype="multipart/form-data">
        @csrf
        @if(isset($toy)) @method('PUT') @endif

        <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start">

            <!-- Left Column -->
            <div>
                <!-- Basic Info -->
                <div class="card" style="margin-bottom:20px">
                    <div class="section-title" style="margin-bottom:20px">📝 Informasi Dasar</div>

                    <div class="form-group">
                        <label class="form-label">Nama Mainan *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $toy->name ?? '') }}" required placeholder="Contoh: Hot Wheels Lamborghini">
                        @error('name')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Brand / Merek</label>
                            <input type="text" name="brand" class="form-control" value="{{ old('brand', $toy->brand ?? '') }}" placeholder="Contoh: Lego, Hot Wheels">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Usia (Range)</label>
                            <input type="text" name="age_range" class="form-control" value="{{ old('age_range', $toy->age_range ?? '') }}" placeholder="Contoh: 3-12">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" placeholder="Deskripsi mainan, kelengkapan, kondisi detail...">{{ old('description', $toy->description ?? '') }}</textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-control">
                                <option value="">— Pilih Kategori —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $toy->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kondisi *</label>
                            <select name="condition" class="form-control" required>
                                @foreach(['new'=>'Baru (New)','like_new'=>'Seperti Baru','good'=>'Bagus','fair'=>'Cukup'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('condition', $toy->condition ?? 'new') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="card" style="margin-bottom:20px">
                    <div class="section-title" style="margin-bottom:20px">💰 Harga</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Harga Beli (Rp) *</label>
                            <input type="number" name="buy_price" class="form-control" value="{{ old('buy_price', $toy->buy_price ?? 0) }}" min="0" step="100" required id="buy_price">
                            @error('buy_price')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Harga Jual (Rp) *</label>
                            <input type="number" name="sell_price" class="form-control" value="{{ old('sell_price', $toy->sell_price ?? 0) }}" min="0" step="100" required id="sell_price">
                            @error('sell_price')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div id="profit-preview" style="background:var(--c-surface);border-radius:8px;padding:12px 16px;font-size:0.875rem;display:flex;gap:24px">
                        <span>Profit: <strong id="profit-val" class="text-teal">Rp 0</strong></span>
                        <span>Margin: <strong id="margin-val" class="text-teal">0%</strong></span>
                    </div>
                </div>

                <!-- Stock -->
                <div class="card">
                    <div class="section-title" style="margin-bottom:20px">📦 Stok</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Jumlah Stok *</label>
                            <input type="number" name="stock" class="form-control" value="{{ old('stock', $toy->stock ?? 0) }}" min="0" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Batas Stok Minimum</label>
                            <input type="number" name="min_stock" class="form-control" value="{{ old('min_stock', $toy->min_stock ?? 5) }}" min="0">
                            <div style="font-size:0.75rem;color:var(--c-muted);margin-top:4px">Notifikasi stok menipis</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div>
                <!-- Image -->
                <div class="card" style="margin-bottom:20px">
                    <div class="section-title" style="margin-bottom:16px">📷 Foto Mainan</div>
                    <div class="img-upload-area" id="img-area">
                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" id="img-input">
                        @if(isset($toy) && $toy->image_url)
                            <img src="{{ $toy->image_url }}" class="img-preview" style="display:block;max-width:100%;max-height:200px" id="img-preview">
                            <div class="text-muted" style="margin-top:8px;font-size:0.8rem">Klik untuk ganti foto</div>
                        @else
                            <div style="font-size:2.5rem;margin-bottom:8px">📸</div>
                            <div style="font-size:0.875rem;color:var(--c-muted)">Klik untuk upload foto</div>
                            <div style="font-size:0.75rem;color:var(--c-muted);margin-top:4px">JPEG, PNG, WebP — max 5MB</div>
                            <img class="img-preview" id="img-preview" style="max-width:100%;max-height:200px">
                        @endif
                    </div>
                </div>

                <!-- Barcode -->
                <div class="card" style="margin-bottom:20px">
                    <div class="section-title" style="margin-bottom:16px">🔖 Barcode & SKU</div>
                    <div class="form-group">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" class="form-control font-mono" value="{{ old('sku', $toy->sku ?? '') }}" placeholder="Auto-generate jika kosong">
                        @error('sku')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Barcode</label>
                        <div style="display:flex;gap:8px">
                            <input type="text" name="barcode" id="barcode-input" class="form-control font-mono" value="{{ old('barcode', $toy->barcode ?? '') }}" placeholder="Auto-generate jika kosong">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="generateBarcode()" title="Generate">🎲</button>
                        </div>
                        @error('barcode')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <!-- Barcode preview -->
                    <div style="text-align:center;margin-top:12px" id="barcode-preview-wrap">
                        @if(isset($toy) && $toy->barcode)
                        <div class="barcode-box">
                            <svg id="barcode-preview" data-barcode="{{ $toy->barcode }}"></svg>
                            <div class="barcode-label">{{ $toy->barcode }}</div>
                        </div>
                        @else
                        <div class="text-muted" style="font-size:0.8rem">Preview barcode muncul setelah diisi</div>
                        @endif
                    </div>
                </div>

                <!-- Status -->
                <div class="card">
                    <div class="section-title" style="margin-bottom:16px">⚙️ Status</div>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $toy->is_active ?? true) ? 'checked' : '' }}
                            style="width:18px;height:18px;accent-color:var(--c-accent)">
                        <span style="font-size:0.875rem">Aktif (tampil di inventori)</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div style="display:flex;gap:12px;margin-top:24px">
            <button type="submit" class="btn btn-primary">
                {{ isset($toy) ? '💾 Simpan Perubahan' : '➕ Tambah Mainan' }}
            </button>
            <a href="{{ isset($toy) ? route('toys.show', $toy) : route('toys.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Profit preview
const buyInput  = document.getElementById('buy_price');
const sellInput = document.getElementById('sell_price');
function updateProfit() {
    const buy  = parseFloat(buyInput.value)  || 0;
    const sell = parseFloat(sellInput.value) || 0;
    const profit = sell - buy;
    const margin = buy > 0 ? ((profit / buy) * 100).toFixed(1) : 0;
    const pEl = document.getElementById('profit-val');
    const mEl = document.getElementById('margin-val');
    pEl.textContent = 'Rp ' + profit.toLocaleString('id-ID');
    mEl.textContent = margin + '%';
    const color = profit >= 0 ? 'var(--c-teal)' : 'var(--c-red)';
    pEl.style.color = mEl.style.color = color;
}
buyInput?.addEventListener('input', updateProfit);
sellInput?.addEventListener('input', updateProfit);
updateProfit();

// Barcode generate
function generateBarcode() {
    const code = '899' + Math.floor(Math.random() * 1e9).toString().padStart(9, '0');
    const input = document.getElementById('barcode-input');
    input.value = code;
    renderBarcodePreview(code);
}

// Barcode live preview
document.getElementById('barcode-input')?.addEventListener('input', function() {
    renderBarcodePreview(this.value);
});

function renderBarcodePreview(code) {
    const wrap = document.getElementById('barcode-preview-wrap');
    if (!code) { wrap.innerHTML = '<div class="text-muted" style="font-size:0.8rem">Preview barcode muncul setelah diisi</div>'; return; }
    wrap.innerHTML = '<div class="barcode-box"><svg id="barcode-preview"></svg><div class="barcode-label">' + code + '</div></div>';
    try {
        JsBarcode('#barcode-preview', code, { format:'CODE128', width:2, height:45, displayValue:false, background:'#ffffff', lineColor:'#000' });
    } catch(e) {}
}

// Image area wiring
const imgArea    = document.getElementById('img-area');
const imgInput   = document.getElementById('img-input');
const imgPreview = document.getElementById('img-preview');

// Klik area → buka file dialog. Tapi jika klik berasal dari input itu sendiri, stop.
imgArea?.addEventListener('click', (e) => {
    if (e.target === imgInput) return; // jangan trigger ulang jika klik dari input
    imgInput?.click();
});

// Stop klik pada input agar tidak naik ke area (mencegah double trigger)
imgInput?.addEventListener('click', (e) => e.stopPropagation());

imgInput?.addEventListener('change', e => {
    const file = e.target.files[0];
    if (!file || !imgPreview) return;
    const reader = new FileReader();
    reader.onload = ev => { imgPreview.src = ev.target.result; imgPreview.style.display = 'block'; };
    reader.readAsDataURL(file);
});
</script>
@endpush
@endsection
