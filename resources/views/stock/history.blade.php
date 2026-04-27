<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0f0e17">
    <title>Riwayat Stok — ToyVault</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:      #0f0e17;
            --surface: #1a1927;
            --card:    #201e30;
            --border:  #2d2b45;
            --accent:  #ff6b35;
            --teal:    #06d6a0;
            --red:     #ef4565;
            --yellow:  #ffd166;
            --muted:   #a7a5c0;
            --text:    #fffffe;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            min-height: 100%;
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
        }

        /* Topbar */
        .topbar {
            position: sticky; top: 0; z-index: 50;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            height: 56px;
            display: flex; align-items: center; padding: 0 16px; gap: 12px;
        }
        .topbar-back {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--card); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            text-decoration: none; font-size: 1.1rem; flex-shrink: 0;
        }
        .topbar-title {
            font-family: 'Syne', sans-serif;
            font-size: 1rem; font-weight: 700; flex: 1;
        }
        .topbar-scan {
            background: var(--accent); color: white;
            border: none; border-radius: 8px;
            padding: 7px 14px; font-size: 0.82rem; font-weight: 600;
            text-decoration: none; cursor: pointer;
        }

        /* Filters */
        .filter-bar {
            padding: 12px 16px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex; gap: 8px; flex-wrap: wrap;
        }
        .filter-input {
            flex: 1; min-width: 160px;
            background: var(--card); border: 1px solid var(--border);
            border-radius: 8px; padding: 8px 12px;
            color: var(--text); font-size: 0.875rem;
        }
        .filter-input:focus { outline: none; border-color: var(--accent); }
        .filter-select {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 8px; padding: 8px 10px;
            color: var(--text); font-size: 0.875rem;
        }
        .filter-btn {
            background: var(--accent); color: white;
            border: none; border-radius: 8px;
            padding: 8px 16px; font-size: 0.875rem;
            cursor: pointer; font-weight: 600;
        }

        /* Stats strip */
        .stats-strip {
            display: flex; gap: 0;
            border-bottom: 1px solid var(--border);
        }
        .stat-item {
            flex: 1; padding: 12px 16px; text-align: center;
            border-right: 1px solid var(--border);
        }
        .stat-item:last-child { border-right: none; }
        .stat-v { font-family: 'Syne', sans-serif; font-size: 1.3rem; font-weight: 800; }
        .stat-l { font-size: 0.7rem; color: var(--muted); text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }

        /* Log list */
        .log-list { padding: 0 0 80px; }
        .log-date-header {
            padding: 8px 16px;
            font-size: 0.72rem; color: var(--muted);
            text-transform: uppercase; letter-spacing: 1.5px;
            background: rgba(255,255,255,0.02);
            border-bottom: 1px solid var(--border);
            position: sticky; top: 56px; z-index: 10;
        }
        .log-item {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            transition: background 0.1s;
        }
        .log-item:hover { background: rgba(255,255,255,0.02); }
        .log-icon {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; flex-shrink: 0;
        }
        .log-icon.in     { background: rgba(6,214,160,.12); }
        .log-icon.out    { background: rgba(239,69,101,.12); }
        .log-icon.adjust { background: rgba(255,107,53,.12); }
        .log-info { flex: 1; min-width: 0; }
        .log-name {
            font-weight: 500; font-size: 0.9rem;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .log-detail { font-size: 0.75rem; color: var(--muted); margin-top: 2px; }
        .log-note {
            font-size: 0.72rem; color: var(--accent);
            margin-top: 2px; font-style: italic;
        }
        .log-right { text-align: right; flex-shrink: 0; }
        .log-qty { font-family: 'Syne', sans-serif; font-size: 1.1rem; font-weight: 700; }
        .log-qty.in     { color: var(--teal); }
        .log-qty.out    { color: var(--red); }
        .log-qty.adjust { color: var(--yellow); }
        .log-stock { font-size: 0.72rem; color: var(--muted); margin-top: 2px; }
        .log-time { font-size: 0.7rem; color: var(--muted); margin-top: 4px; }

        /* Empty */
        .empty-state {
            text-align: center; padding: 60px 24px;
            color: var(--muted);
        }
        .empty-icon { font-size: 3rem; margin-bottom: 12px; }

        /* Pagination */
        .pagination-wrap {
            display: flex; gap: 6px; flex-wrap: wrap;
            padding: 16px; justify-content: center;
        }
        .pagination-wrap a, .pagination-wrap span {
            padding: 7px 13px; border-radius: 7px;
            font-size: 0.82rem; text-decoration: none;
            color: var(--muted); background: var(--card);
            border: 1px solid var(--border);
        }
        .pagination-wrap a:hover { border-color: var(--accent); color: var(--accent); }
        .pagination-wrap [aria-current="page"] span {
            background: var(--accent); color: white; border-color: var(--accent);
        }

        /* FAB */
        .fab {
            position: fixed; bottom: 24px; right: 20px;
            width: 56px; height: 56px; border-radius: 50%;
            background: var(--accent); color: white;
            border: none; font-size: 1.5rem;
            display: flex; align-items: center; justify-content: center;
            text-decoration: none; box-shadow: 0 4px 20px rgba(255,107,53,0.4);
            transition: transform 0.15s;
        }
        .fab:hover { transform: scale(1.08); }
    </style>
</head>
<body>

<div class="topbar">
    <a href="{{ route('stock.scanner') }}" class="topbar-back">←</a>
    <div class="topbar-title">📋 Riwayat Stok</div>
    <a href="{{ route('stock.scanner') }}" class="topbar-scan">📷 Scanner</a>
</div>

<!-- Filter -->
<form method="GET" action="{{ route('stock.history') }}">
<div class="filter-bar">
    <input type="text" name="search" class="filter-input"
        value="{{ request('search') }}" placeholder="🔍 Cari nama / SKU...">
    <select name="type" class="filter-select">
        <option value="">Semua Tipe</option>
        <option value="in"     {{ request('type') === 'in'     ? 'selected' : '' }}>📥 Stok Masuk</option>
        <option value="out"    {{ request('type') === 'out'    ? 'selected' : '' }}>📤 Stok Keluar</option>
        <option value="adjust" {{ request('type') === 'adjust' ? 'selected' : '' }}>⚙️ Penyesuaian</option>
    </select>
    <button type="submit" class="filter-btn">Cari</button>
</div>
</form>

<!-- Stats -->
@php
    $totalIn     = \App\Models\StockLog::where('type', 'in')->sum('qty');
    $totalOut    = \App\Models\StockLog::where('type', 'out')->sum('qty');
    $totalTrx    = \App\Models\StockLog::count();
@endphp
<div class="stats-strip">
    <div class="stat-item">
        <div class="stat-v" style="color:var(--teal)">{{ number_format($totalIn) }}</div>
        <div class="stat-l">Total Masuk</div>
    </div>
    <div class="stat-item">
        <div class="stat-v" style="color:var(--red)">{{ number_format($totalOut) }}</div>
        <div class="stat-l">Total Keluar</div>
    </div>
    <div class="stat-item">
        <div class="stat-v">{{ number_format($totalTrx) }}</div>
        <div class="stat-l">Transaksi</div>
    </div>
</div>

<!-- Log list -->
<div class="log-list">
@php
    $typeIcon  = ['in' => '📥', 'out' => '📤', 'adjust' => '⚙️'];
    $typeLabel = ['in' => 'Stok Masuk', 'out' => 'Stok Keluar', 'adjust' => 'Penyesuaian'];
    $prevDate  = null;
@endphp

@forelse($logs as $log)
@php $date = $log->created_at->format('d M Y'); @endphp
@if($date !== $prevDate)
    <div class="log-date-header">{{ $date }}</div>
    @php $prevDate = $date; @endphp
@endif

<div class="log-item">
    <div class="log-icon {{ $log->type }}">{{ $typeIcon[$log->type] ?? '📦' }}</div>
    <div class="log-info">
        <div class="log-name">{{ $log->toy?->name ?? '—' }}</div>
        <div class="log-detail">
            {{ $typeLabel[$log->type] ?? $log->type }}
            · SKU: {{ $log->toy?->sku ?? '—' }}
        </div>
        @if($log->note)
        <div class="log-note">"{{ $log->note }}"</div>
        @endif
    </div>
    <div class="log-right">
        <div class="log-qty {{ $log->type }}">
            {{ $log->type === 'in' ? '+' : ($log->type === 'out' ? '-' : '=') }}{{ $log->qty }}
        </div>
        <div class="log-stock">{{ $log->before }} → {{ $log->after }} pcs</div>
        <div class="log-time">{{ $log->created_at->format('H:i') }}</div>
    </div>
</div>

@empty
<div class="empty-state">
    <div class="empty-icon">📭</div>
    <div>Belum ada riwayat transaksi stok.</div>
    <div style="margin-top:8px;font-size:0.82rem">Mulai dengan scan barcode mainan.</div>
</div>
@endforelse

@if($logs->hasPages())
<div class="pagination-wrap">
    {{ $logs->links() }}
</div>
@endif
</div>

<!-- FAB Scanner -->
<a href="{{ route('stock.scanner') }}" class="fab">📷</a>

</body>
</html>
