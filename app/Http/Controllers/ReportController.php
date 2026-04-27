<?php

namespace App\Http\Controllers;

use App\Models\Toy;
use App\Models\Category;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Export daftar mainan ke CSV
     */
    public function exportCsv(Request $request)
    {
        $query = Toy::with('category');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        $toys = $query->orderBy('name')->get();

        $filename = 'inventory_mainan_' . date('Ymd_His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($toys) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($handle, [
                'Nama', 'SKU', 'Barcode', 'Kategori', 'Brand',
                'Kondisi', 'Usia', 'Harga Beli', 'Harga Jual',
                'Profit', 'Margin (%)', 'Stok', 'Min Stok',
                'Status', 'Tanggal Ditambahkan',
            ]);

            $condLabels = ['new' => 'Baru', 'like_new' => 'Seperti Baru', 'good' => 'Bagus', 'fair' => 'Cukup'];

            foreach ($toys as $toy) {
                fputcsv($handle, [
                    $toy->name,
                    $toy->sku,
                    $toy->barcode ?? '',
                    $toy->category->name ?? '',
                    $toy->brand ?? '',
                    $condLabels[$toy->condition] ?? $toy->condition,
                    $toy->age_range ?? '',
                    $toy->buy_price,
                    $toy->sell_price,
                    $toy->profit,
                    $toy->profit_percent,
                    $toy->stock,
                    $toy->min_stock,
                    $toy->is_active ? 'Aktif' : 'Nonaktif',
                    $toy->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Laporan ringkasan per kategori
     */
    public function summary()
    {
        $byCategory = Category::withCount('toys')
            ->withSum('toys', 'stock')
            ->with(['toys' => function ($q) {
                $q->selectRaw('category_id, SUM(stock * buy_price) as total_buy_value, SUM(stock * sell_price) as total_sell_value')
                  ->groupBy('category_id');
            }])
            ->get()
            ->map(function ($cat) {
                $aggr = Toy::where('category_id', $cat->id)
                    ->selectRaw('SUM(stock * buy_price) as buy_val, SUM(stock * sell_price) as sell_val, AVG(sell_price - buy_price) as avg_profit')
                    ->first();
                return [
                    'name'       => $cat->name,
                    'color'      => $cat->color,
                    'count'      => $cat->toys_count,
                    'stock'      => $cat->toys_sum_stock ?? 0,
                    'buy_value'  => $aggr->buy_val ?? 0,
                    'sell_value' => $aggr->sell_val ?? 0,
                    'avg_profit' => round($aggr->avg_profit ?? 0),
                ];
            });

        $overall = [
            'total_items'      => Toy::count(),
            'total_stock'      => Toy::sum('stock'),
            'total_buy_value'  => Toy::selectRaw('SUM(stock * buy_price) as t')->value('t') ?? 0,
            'total_sell_value' => Toy::selectRaw('SUM(stock * sell_price) as t')->value('t') ?? 0,
            'low_stock_count'  => Toy::whereColumn('stock', '<=', 'min_stock')->count(),
            'out_of_stock'     => Toy::where('stock', 0)->count(),
        ];

        return view('reports.summary', compact('byCategory', 'overall'));
    }
}
