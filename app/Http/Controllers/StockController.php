<?php

namespace App\Http\Controllers;

use App\Models\Toy;
use App\Models\StockLog;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /** Halaman scanner mobile */
    public function scanner()
    {
        return view('stock.scanner');
    }

    /** API: cari mainan berdasarkan barcode (JSON) */
    public function findByBarcode(Request $request)
    {
        $barcode = trim($request->get('barcode', ''));

        if (empty($barcode)) {
            return response()->json(['found' => false, 'message' => 'Barcode kosong.'], 400);
        }

        $toy = Toy::with('category')
            ->where('barcode', $barcode)
            ->orWhere('sku', $barcode)
            ->first();

        if (!$toy) {
            return response()->json(['found' => false, 'message' => 'Mainan tidak ditemukan untuk barcode: ' . $barcode], 404);
        }

        return response()->json([
            'found' => true,
            'toy'   => [
                'id'         => $toy->id,
                'name'       => $toy->name,
                'sku'        => $toy->sku,
                'barcode'    => $toy->barcode,
                'brand'      => $toy->brand,
                'category'   => $toy->category?->name,
                'stock'      => $toy->stock,
                'min_stock'  => $toy->min_stock,
                'buy_price'  => $toy->buy_price,
                'sell_price' => $toy->sell_price,
                'image_url'  => $toy->image_url,
                'is_low_stock' => $toy->is_low_stock,
            ],
        ]);
    }

    /** Proses stok in / out dari scanner */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'toy_id' => 'required|exists:toys,id',
            'type'   => 'required|in:in,out',
            'qty'    => 'required|integer|min:1|max:9999',
            'note'   => 'nullable|string|max:255',
        ]);

        $toy    = Toy::findOrFail($validated['toy_id']);
        $before = $toy->stock;

        if ($validated['type'] === 'out' && $toy->stock < $validated['qty']) {
            return response()->json([
                'success' => false,
                'message' => "Stok tidak cukup! Stok tersedia: {$toy->stock} pcs.",
            ], 422);
        }

        if ($validated['type'] === 'in') {
            $toy->increment('stock', $validated['qty']);
        } else {
            $toy->decrement('stock', $validated['qty']);
        }

        $after = $toy->fresh()->stock;

        StockLog::create([
            'toy_id' => $toy->id,
            'type'   => $validated['type'],
            'qty'    => $validated['qty'],
            'before' => $before,
            'after'  => $after,
            'note'   => $validated['note'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => ($validated['type'] === 'in' ? '📥 Stok masuk' : '📤 Stok keluar')
                       . " {$validated['qty']} pcs berhasil dicatat.",
            'toy' => [
                'id'           => $toy->id,
                'name'         => $toy->name,
                'stock'        => $after,
                'is_low_stock' => $toy->fresh()->is_low_stock,
            ],
        ]);
    }

    /** Riwayat stok (semua transaksi) */
    public function history(Request $request)
    {
        $logs = StockLog::with('toy')
            ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
            ->when($request->filled('search'), fn($q) => $q->whereHas('toy', fn($tq) =>
                $tq->where('name', 'like', '%' . $request->search . '%')
                   ->orWhere('sku', 'like', '%' . $request->search . '%')
            ))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('stock.history', compact('logs'));
    }

    /** Adjust stok dari halaman detail (existing) */
    public function adjust(Request $request, Toy $toy)
    {
        $validated = $request->validate([
            'type' => 'required|in:in,out,adjust',
            'qty'  => 'required|integer|min:1',
            'note' => 'nullable|string|max:255',
        ]);

        $before = $toy->stock;

        switch ($validated['type']) {
            case 'in':
                $toy->increment('stock', $validated['qty']);
                break;
            case 'out':
                if ($toy->stock < $validated['qty']) {
                    return back()->with('error', 'Stok tidak mencukupi!');
                }
                $toy->decrement('stock', $validated['qty']);
                break;
            case 'adjust':
                $toy->update(['stock' => $validated['qty']]);
                break;
        }

        StockLog::create([
            'toy_id' => $toy->id,
            'type'   => $validated['type'],
            'qty'    => $validated['qty'],
            'before' => $before,
            'after'  => $toy->fresh()->stock,
            'note'   => $validated['note'] ?? null,
        ]);

        $typeLabel = ['in' => 'masuk', 'out' => 'keluar', 'adjust' => 'disesuaikan'];
        return back()->with('success', "Stok {$toy->name} berhasil {$typeLabel[$validated['type']]}.");
    }
}
