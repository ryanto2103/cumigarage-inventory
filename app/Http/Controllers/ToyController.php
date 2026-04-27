<?php

namespace App\Http\Controllers;

use App\Models\Toy;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ToyController extends Controller
{
    public function index(Request $request)
    {
        $query = Toy::with('category');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->lowStock();
            } elseif ($request->stock_status === 'out') {
                $query->where('stock', 0);
            }
        }

        $sortBy   = $request->get('sort', 'created_at');
        $sortDir  = $request->get('dir', 'desc');
        $allowed  = ['name', 'sku', 'buy_price', 'sell_price', 'stock', 'created_at'];
        if (in_array($sortBy, $allowed)) {
            $query->orderBy($sortBy, $sortDir);
        }

        $toys       = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        $stats = [
            'total'       => Toy::count(),
            'low_stock'   => Toy::lowStock()->count(),
            'out_stock'   => Toy::where('stock', 0)->count(),
            'total_value' => Toy::selectRaw('SUM(stock * buy_price) as total')->value('total') ?? 0,
        ];

        return view('toys.index', compact('toys', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('toys.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'sku'         => 'nullable|string|max:100|unique:toys,sku',
            'barcode'     => 'nullable|string|max:100|unique:toys,barcode',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'buy_price'   => 'required|numeric|min:0',
            'sell_price'  => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'min_stock'   => 'nullable|integer|min:0',
            'condition'   => 'required|in:new,like_new,good,fair',
            'brand'       => 'nullable|string|max:100',
            'age_range'   => 'nullable|string|max:50',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_active'   => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $this->uploadImage($request->file('image'));
        }

        // Auto-generate barcode if empty
        if (empty($validated['barcode'])) {
            $validated['barcode'] = $this->generateUniqueBarcode();
        }

        $toy = Toy::create($validated);

        return redirect()->route('toys.show', $toy)
            ->with('success', "Mainan \"{$toy->name}\" berhasil ditambahkan!");
    }

    public function show(Toy $toy)
    {
        $toy->load('category');
        $barcodeSvg = $this->generateBarcodeSvg($toy->barcode);
        return view('toys.show', compact('toy', 'barcodeSvg'));
    }

    public function edit(Toy $toy)
    {
        $categories = Category::orderBy('name')->get();
        return view('toys.edit', compact('toy', 'categories'));
    }

    public function update(Request $request, Toy $toy)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'sku'         => 'nullable|string|max:100|unique:toys,sku,' . $toy->id,
            'barcode'     => 'nullable|string|max:100|unique:toys,barcode,' . $toy->id,
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'buy_price'   => 'required|numeric|min:0',
            'sell_price'  => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'min_stock'   => 'nullable|integer|min:0',
            'condition'   => 'required|in:new,like_new,good,fair',
            'brand'       => 'nullable|string|max:100',
            'age_range'   => 'nullable|string|max:50',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_active'   => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($toy->image) {
                @unlink(public_path('uploads/toys/' . $toy->image));
            }
            $validated['image'] = $this->uploadImage($request->file('image'));
        }

        $validated['is_active'] = $request->boolean('is_active');
        $toy->update($validated);

        return redirect()->route('toys.show', $toy)
            ->with('success', "Mainan \"{$toy->name}\" berhasil diperbarui!");
    }

    public function destroy(Toy $toy)
    {
        $name = $toy->name;
        if ($toy->image) {
            @unlink(public_path('uploads/toys/' . $toy->image));
        }
        $toy->delete();
        return redirect()->route('toys.index')
            ->with('success', "Mainan \"{$name}\" berhasil dihapus.");
    }

    public function barcode(Toy $toy)
    {
        // Return barcode image for printing
        $barcodeSvg = $this->generateBarcodeSvg($toy->barcode);
        return view('toys.barcode', compact('toy', 'barcodeSvg'));
    }

    public function dashboard()
    {
        $stats = [
            'total'         => Toy::count(),
            'active'        => Toy::active()->count(),
            'low_stock'     => Toy::lowStock()->count(),
            'out_stock'     => Toy::where('stock', 0)->count(),
            'total_buy'     => Toy::selectRaw('SUM(stock * buy_price) as t')->value('t') ?? 0,
            'total_sell'    => Toy::selectRaw('SUM(stock * sell_price) as t')->value('t') ?? 0,
            'categories'    => Category::withCount('toys')->get(),
        ];

        $low_stock_toys  = Toy::with('category')->lowStock()->orderBy('stock')->take(10)->get();
        $latest_toys     = Toy::with('category')->latest()->take(8)->get();

        return view('dashboard', compact('stats', 'low_stock_toys', 'latest_toys'));
    }

    // ---- Private Helpers ----

    private function uploadImage($file): string
    {
        $filename  = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $directory = public_path('uploads/toys');
        if (!is_dir($directory)) mkdir($directory, 0755, true);
        $file->move($directory, $filename);
        return $filename;
    }

    private function generateUniqueBarcode(): string
    {
        do {
            $code = '899' . str_pad(random_int(0, 999999999), 9, '0', STR_PAD_LEFT);
        } while (Toy::where('barcode', $code)->exists());
        return $code;
    }

    private function generateBarcodeSvg(string $barcode): string
    {
        // Simple inline barcode SVG generator (Code128-style visual)
        // In production, use picqer/php-barcode-generator package
        return $barcode; // Passed to view; JS library handles rendering
    }
}
