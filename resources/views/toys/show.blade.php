@extends('layouts.app')
@section('topbar-title', $toy->name)

@section('topbar-actions')
<a href="{{ route('toys.barcode', $toy) }}" target="_blank" class="btn btn-secondary">🖨️ Print Barcode</a>
<a href="{{ route('toys.edit', $toy) }}" class="btn btn-secondary">✏️ Edit</a>
<form method="POST" action="{{ route('toys.destroy', $toy) }}" style="display:inline" onsubmit="return confirm('Hapus mainan ini?')">
    @csrf @method('DELETE')
    <button type="submit" class="btn btn-danger">🗑️ Hapus</button>
</form>
@endsection

@section('content')
<div style="display:grid;grid-template-columns:340px 1fr;gap:28px;align-items:start">

    <!-- Left: Image + Barcode -->
    <div>
        <div class="card" style="margin-bottom:20px">
            @if($toy->image_url)
                <img src="{{ $toy->image_url }}" alt="{{ $toy->name }}" style="width:100%;border-radius:10px;object-fit:cover;max-height:320px">
            @else
                <div style="width:100%;height:240px;background:var(--c-surface);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:5rem">🧸</div>
            @endif
        </div>

        <!-- Barcode -->
        <div class="card" style="text-align:center">
            <div class="section-title" style="margin-bottom:16px">🔖 Barcode</div>
            @if($toy->barcode)
            <div class="barcode-box" style="display:inline-block">
                <svg data-barcode="{{ $toy->barcode }}" id="main-barcode"></svg>
                <div class="barcode-label">{{ $toy->barcode }}</div>
            </div>
            @else
            <div class="text-muted">Tidak ada barcode</div>
            @endif
            <div style="margin-top:12px">
                <div style="font-size:0.8rem;color:var(--c-muted)">SKU</div>
                <div class="font-mono" style="font-size:0.9rem;color:var(--c-accent2)">{{ $toy->sku }}</div>
            </div>
        </div>
    </div>

    <!-- Right: Details -->
    <div>
        <div class="card" style="margin-bottom:20px">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px">
                <div>
                    <h1 style="font-family:var(--font-head);font-size:1.7rem;font-weight:800;line-height:1.2">{{ $toy->name }}</h1>
                    @if($toy->brand)
                    <div class="text-muted" style="margin-top:4px">{{ $toy->brand }}</div>
                    @endif
                </div>
                <div>
                    @php $condLabels = ['new'=>'Baru','like_new'=>'Spt Baru','good'=>'Bagus','fair'=>'Cukup']; @endphp
                    <span class="badge badge-{{ $toy->condition }}">{{ $condLabels[$toy->condition] ?? $toy->condition }}</span>
                    @if(!$toy->is_active)
                    <span class="badge badge-fair" style="margin-left:6px">Nonaktif</span>
                    @endif
                </div>
            </div>

            @if($toy->description)
            <p style="color:var(--c-muted);font-size:0.9rem;line-height:1.6;margin-bottom:20px">{{ $toy->description }}</p>
            @endif

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                @if($toy->category)
                <div>
                    <div class="stat-label">Kategori</div>
                    <div style="display:flex;align-items:center;gap:6px">
                        <span style="width:10px;height:10px;border-radius:50%;background:{{ $toy->category->color }}"></span>
                        <span>{{ $toy->category->name }}</span>
                    </div>
                </div>
                @endif
                @if($toy->age_range)
                <div>
                    <div class="stat-label">Usia</div>
                    <div>{{ $toy->age_range }} tahun</div>
                </div>
                @endif
                <div>
                    <div class="stat-label">Ditambahkan</div>
                    <div>{{ $toy->created_at->format('d M Y') }}</div>
                </div>
                <div>
                    <div class="stat-label">Terakhir Diperbarui</div>
                    <div>{{ $toy->updated_at->format('d M Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Pricing Card -->
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:20px">
            <div class="stat-card yellow">
                <div class="stat-label">Harga Beli</div>
                <div class="stat-value" style="font-size:1.2rem">Rp {{ number_format($toy->buy_price, 0, ',', '.') }}</div>
                <div class="stat-sub">Modal</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-label">Harga Jual</div>
                <div class="stat-value" style="font-size:1.2rem">Rp {{ number_format($toy->sell_price, 0, ',', '.') }}</div>
                <div class="stat-sub">Retail</div>
            </div>
            <div class="stat-card teal">
                <div class="stat-label">Profit</div>
                <div class="stat-value {{ $toy->profit >= 0 ? 'text-teal' : 'text-red' }}" style="font-size:1.2rem">
                    Rp {{ number_format($toy->profit, 0, ',', '.') }}
                </div>
                <div class="stat-sub">Margin {{ $toy->profit_percent }}%</div>
            </div>
        </div>

        <!-- Stock Card -->
        <div class="stat-card {{ $toy->is_low_stock ? 'red' : 'teal' }}">
            <div style="display:flex;align-items:center;justify-content:space-between">
                <div>
                    <div class="stat-label">Stok Saat Ini</div>
                    <div class="stat-value" style="font-size:2.5rem">{{ $toy->stock }}</div>
                    <div class="stat-sub">Min. stok: {{ $toy->min_stock }} pcs
                        @if($toy->is_low_stock)
                        <span class="text-red" style="margin-left:8px">⚠️ Stok menipis!</span>
                        @endif
                    </div>
                </div>
                <div style="text-align:right">
                    <div class="stat-label">Nilai Stok (Beli)</div>
                    <div style="font-size:1.1rem;font-weight:700">Rp {{ number_format($toy->stock * $toy->buy_price, 0, ',', '.') }}</div>
                    <div class="stat-label" style="margin-top:8px">Nilai Stok (Jual)</div>
                    <div style="font-size:1.1rem;font-weight:700;color:var(--c-teal)">Rp {{ number_format($toy->stock * $toy->sell_price, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
