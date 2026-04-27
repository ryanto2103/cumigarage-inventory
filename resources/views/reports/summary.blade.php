@extends('layouts.app')
@section('topbar-title', 'Laporan Ringkasan')

@section('topbar-actions')
<a href="{{ route('reports.export') }}" class="btn btn-secondary">📥 Export CSV</a>
@endsection

@section('content')
<!-- Overall Stats -->
<div class="grid-4" style="margin-bottom:24px">
    <div class="stat-card orange">
        <div class="stat-label">Total Item</div>
        <div class="stat-value">{{ number_format($overall['total_items']) }}</div>
        <div class="stat-sub">{{ number_format($overall['total_stock']) }} pcs stok</div>
    </div>
    <div class="stat-card yellow">
        <div class="stat-label">Total Nilai Beli</div>
        <div class="stat-value" style="font-size:1.2rem">Rp {{ number_format($overall['total_buy_value'], 0, ',', '.') }}</div>
        <div class="stat-sub">Modal keseluruhan</div>
    </div>
    <div class="stat-card teal">
        <div class="stat-label">Total Nilai Jual</div>
        <div class="stat-value" style="font-size:1.2rem">Rp {{ number_format($overall['total_sell_value'], 0, ',', '.') }}</div>
        <div class="stat-sub">Potensi omzet</div>
    </div>
    <div class="stat-card red">
        <div class="stat-label">Potensial Profit</div>
        @php $totalProfit = $overall['total_sell_value'] - $overall['total_buy_value']; @endphp
        <div class="stat-value" style="font-size:1.2rem;color:var(--c-teal)">Rp {{ number_format($totalProfit, 0, ',', '.') }}</div>
        <div class="stat-sub">Jual - Modal</div>
    </div>
</div>

<!-- Per Category -->
<div class="card">
    <div class="section-header" style="margin-bottom:20px">
        <div class="section-title">📊 Ringkasan per Kategori</div>
        <a href="{{ route('reports.export') }}" class="btn btn-secondary btn-sm">📥 Export CSV</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Jumlah Item</th>
                    <th>Total Stok</th>
                    <th>Nilai Beli</th>
                    <th>Nilai Jual</th>
                    <th>Potensi Profit</th>
                    <th>Rata² Profit/item</th>
                </tr>
            </thead>
            <tbody>
                @foreach($byCategory as $cat)
                <tr>
                    <td>
                        <span style="display:inline-flex;align-items:center;gap:6px">
                            <span style="width:10px;height:10px;border-radius:50%;background:{{ $cat['color'] }}"></span>
                            {{ $cat['name'] }}
                        </span>
                    </td>
                    <td>{{ $cat['count'] }}</td>
                    <td>{{ number_format($cat['stock']) }} pcs</td>
                    <td>Rp {{ number_format($cat['buy_value'], 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($cat['sell_value'], 0, ',', '.') }}</td>
                    <td class="text-teal">Rp {{ number_format($cat['sell_value'] - $cat['buy_value'], 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($cat['avg_profit'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="border-top:2px solid var(--c-border)">
                    <td style="font-weight:600">TOTAL</td>
                    <td style="font-weight:600">{{ number_format($overall['total_items']) }}</td>
                    <td style="font-weight:600">{{ number_format($overall['total_stock']) }} pcs</td>
                    <td style="font-weight:600">Rp {{ number_format($overall['total_buy_value'], 0, ',', '.') }}</td>
                    <td style="font-weight:600">Rp {{ number_format($overall['total_sell_value'], 0, ',', '.') }}</td>
                    <td style="font-weight:600" class="text-teal">Rp {{ number_format($totalProfit, 0, ',', '.') }}</td>
                    <td>—</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Alerts -->
@if($overall['low_stock_count'] > 0 || $overall['out_of_stock'] > 0)
<div class="card" style="margin-top:24px;border-color:rgba(239,69,101,0.3)">
    <div class="section-title" style="margin-bottom:12px;color:var(--c-red)">⚠️ Peringatan Stok</div>
    <div style="display:flex;gap:20px;font-size:0.875rem">
        <span>Stok menipis: <strong class="text-red">{{ $overall['low_stock_count'] }}</strong> item</span>
        <span>Stok habis: <strong class="text-red">{{ $overall['out_of_stock'] }}</strong> item</span>
        <a href="{{ route('toys.index', ['stock_status' => 'low']) }}" class="text-accent">→ Lihat daftar</a>
    </div>
</div>
@endif
@endsection
