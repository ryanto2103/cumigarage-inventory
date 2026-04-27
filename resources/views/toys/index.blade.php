@extends('layouts.app')
@section('topbar-title', 'Koleksi Mainan')

@section('content')
<!-- Stats bar -->
<div style="display:flex;gap:16px;margin-bottom:24px;flex-wrap:wrap">
    <div style="background:var(--c-card);border:1px solid var(--c-border);border-radius:8px;padding:10px 18px;font-size:0.85rem">
        <span class="text-muted">Total: </span><strong>{{ $stats['total'] }}</strong>
    </div>
    <div style="background:var(--c-card);border:1px solid var(--c-border);border-radius:8px;padding:10px 18px;font-size:0.85rem">
        <span class="text-muted">Stok menipis: </span><strong class="text-red">{{ $stats['low_stock'] }}</strong>
    </div>
    <div style="background:var(--c-card);border:1px solid var(--c-border);border-radius:8px;padding:10px 18px;font-size:0.85rem">
        <span class="text-muted">Nilai stok: </span><strong class="text-teal">Rp {{ number_format($stats['total_value'], 0, ',', '.') }}</strong>
    </div>
</div>

<!-- Filters -->
<form method="GET" action="{{ route('toys.index') }}">
<div class="search-bar" style="margin-bottom:20px">
    <input type="text" name="search" value="{{ request('search') }}"
        placeholder="🔍 Cari nama, SKU, barcode, brand..."
        class="form-control" style="max-width:320px">

    <select name="category" class="form-control" style="max-width:180px">
        <option value="">Semua Kategori</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
        @endforeach
    </select>

    <select name="condition" class="form-control" style="max-width:160px">
        <option value="">Semua Kondisi</option>
        <option value="new"      {{ request('condition') == 'new'      ? 'selected' : '' }}>Baru</option>
        <option value="like_new" {{ request('condition') == 'like_new' ? 'selected' : '' }}>Seperti Baru</option>
        <option value="good"     {{ request('condition') == 'good'     ? 'selected' : '' }}>Bagus</option>
        <option value="fair"     {{ request('condition') == 'fair'     ? 'selected' : '' }}>Cukup</option>
    </select>

    <select name="stock_status" class="form-control" style="max-width:160px">
        <option value="">Semua Stok</option>
        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stok Menipis</option>
        <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Habis</option>
    </select>

    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="{{ route('toys.index') }}" class="btn btn-secondary">Reset</a>
</div>
</form>

<!-- Table -->
@if($toys->count())
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Mainan</th>
                <th>SKU / Barcode</th>
                <th>Kategori</th>
                <th>Kondisi</th>
                <th>Harga Beli</th>
                <th>Harga Jual</th>
                <th>Profit</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($toys as $toy)
            <tr>
                <td>
                    <div class="flex items-center gap-2" style="gap:12px">
                        @if($toy->image_url)
                            <img src="{{ $toy->image_url }}" class="toy-img" alt="{{ $toy->name }}">
                        @else
                            <div class="toy-img-placeholder">🧸</div>
                        @endif
                        <div>
                            <div style="font-weight:500">{{ $toy->name }}</div>
                            @if($toy->brand)
                            <div class="text-muted" style="font-size:0.78rem">{{ $toy->brand }}</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td>
                    <div class="font-mono text-muted">{{ $toy->sku }}</div>
                    @if($toy->barcode)
                    <div class="font-mono" style="color:var(--c-accent2);font-size:0.72rem">{{ $toy->barcode }}</div>
                    @endif
                </td>
                <td>
                    @if($toy->category)
                    <span style="display:inline-flex;align-items:center;gap:5px;font-size:0.82rem">
                        <span style="width:8px;height:8px;border-radius:50%;background:{{ $toy->category->color }}"></span>
                        {{ $toy->category->name }}
                    </span>
                    @else
                    <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    @php
                        $condLabels = ['new'=>'Baru','like_new'=>'Spt Baru','good'=>'Bagus','fair'=>'Cukup'];
                    @endphp
                    <span class="badge badge-{{ $toy->condition }}">{{ $condLabels[$toy->condition] ?? $toy->condition }}</span>
                </td>
                <td style="white-space:nowrap">Rp {{ number_format($toy->buy_price, 0, ',', '.') }}</td>
                <td style="white-space:nowrap">Rp {{ number_format($toy->sell_price, 0, ',', '.') }}</td>
                <td>
                    <span class="{{ $toy->profit >= 0 ? 'text-teal' : 'text-red' }}" style="font-size:0.85rem">
                        {{ $toy->profit_percent }}%
                    </span>
                </td>
                <td>
                    <span class="badge {{ $toy->is_low_stock ? 'badge-low' : 'badge-ok' }}">{{ $toy->stock }}</span>
                </td>
                <td>
                    <div class="flex gap-2" style="gap:6px">
                        <a href="{{ route('toys.show', $toy) }}" class="btn btn-secondary btn-sm">Detail</a>
                        <a href="{{ route('toys.edit', $toy) }}" class="btn btn-secondary btn-sm">Edit</a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="pagination">
    {{ $toys->links() }}
</div>

@else
<div class="card" style="text-align:center;padding:60px">
    <div style="font-size:4rem;margin-bottom:16px">🧸</div>
    <div class="section-title" style="margin-bottom:8px">Belum ada mainan</div>
    <div class="text-muted" style="margin-bottom:24px;font-size:0.875rem">Tambahkan mainan pertama ke koleksi Anda</div>
    <a href="{{ route('toys.create') }}" class="btn btn-primary">+ Tambah Mainan</a>
</div>
@endif
@endsection
