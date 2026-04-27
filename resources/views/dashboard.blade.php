@extends('layouts.app')
@section('topbar-title', 'Dashboard')

@section('content')
<div class="grid-4 mb-4" style="margin-bottom:28px">
    <div class="stat-card orange">
        <div class="stat-label">Total Mainan</div>
        <div class="stat-value">{{ number_format($stats['total']) }}</div>
        <div class="stat-sub">{{ $stats['active'] }} aktif</div>
    </div>
    <div class="stat-card yellow">
        <div class="stat-label">Nilai Beli (Stok)</div>
        <div class="stat-value" style="font-size:1.3rem">Rp {{ number_format($stats['total_buy'], 0, ',', '.') }}</div>
        <div class="stat-sub">Harga modal × stok</div>
    </div>
    <div class="stat-card teal">
        <div class="stat-label">Nilai Jual (Stok)</div>
        <div class="stat-value" style="font-size:1.3rem">Rp {{ number_format($stats['total_sell'], 0, ',', '.') }}</div>
        <div class="stat-sub">Potensi omzet</div>
    </div>
    <div class="stat-card red">
        <div class="stat-label">Stok Menipis</div>
        <div class="stat-value text-red">{{ $stats['low_stock'] }}</div>
        <div class="stat-sub">{{ $stats['out_stock'] }} habis</div>
    </div>
</div>

<div class="grid-3" style="grid-template-columns: 1fr 1fr; gap:24px">
    <!-- Low Stock Warning -->
    <div class="card">
        <div class="section-header">
            <div class="section-title">⚠️ Stok Menipis</div>
            <a href="{{ route('toys.index', ['stock_status' => 'low']) }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>
        @forelse($low_stock_toys as $toy)
        <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--c-border)">
            @if($toy->image_url)
                <img src="{{ $toy->image_url }}" class="toy-img" alt="">
            @else
                <div class="toy-img-placeholder">🧸</div>
            @endif
            <div style="flex:1;min-width:0">
                <div style="font-weight:500;font-size:0.875rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $toy->name }}</div>
                <div class="text-muted" style="font-size:0.78rem">{{ $toy->sku }}</div>
            </div>
            <div style="text-align:right">
                <div class="badge {{ $toy->stock == 0 ? 'badge-fair' : 'badge-low' }}">{{ $toy->stock }} pcs</div>
            </div>
        </div>
        @empty
        <div class="text-muted" style="text-align:center;padding:24px;font-size:0.875rem">✅ Semua stok aman</div>
        @endforelse
    </div>

    <!-- Latest Additions -->
    <div class="card">
        <div class="section-header">
            <div class="section-title">🆕 Baru Ditambahkan</div>
            <a href="{{ route('toys.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>
        @foreach($latest_toys as $toy)
        <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--c-border)">
            @if($toy->image_url)
                <img src="{{ $toy->image_url }}" class="toy-img" alt="">
            @else
                <div class="toy-img-placeholder">🧸</div>
            @endif
            <div style="flex:1;min-width:0">
                <div style="font-weight:500;font-size:0.875rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $toy->name }}</div>
                <div class="text-muted" style="font-size:0.78rem">Rp {{ number_format($toy->sell_price, 0, ',', '.') }}</div>
            </div>
            <a href="{{ route('toys.show', $toy) }}" class="btn btn-secondary btn-sm">Detail</a>
        </div>
        @endforeach
    </div>
</div>

<!-- Categories Overview -->
@if($stats['categories']->count())
<div class="card mt-6" style="margin-top:24px">
    <div class="section-header">
        <div class="section-title">📦 Kategori</div>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary btn-sm">Kelola</a>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:10px">
        @foreach($stats['categories'] as $cat)
        <a href="{{ route('toys.index', ['category' => $cat->id]) }}" style="text-decoration:none">
            <div style="background:var(--c-surface);border:1px solid var(--c-border);border-radius:8px;padding:10px 16px;display:flex;align-items:center;gap:8px;transition:border-color 0.15s" onmouseover="this.style.borderColor='{{ $cat->color }}'" onmouseout="this.style.borderColor='var(--c-border)'">
                <span style="width:10px;height:10px;border-radius:50%;background:{{ $cat->color }};flex-shrink:0"></span>
                <span style="font-size:0.875rem;font-weight:500">{{ $cat->name }}</span>
                <span class="text-muted" style="font-size:0.8rem">{{ $cat->toys_count }}</span>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif
@endsection
