<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Toy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'sku', 'barcode', 'category_id', 'description',
        'buy_price', 'sell_price', 'stock', 'min_stock',
        'condition', 'brand', 'age_range', 'image', 'is_active',
    ];

    protected $casts = [
        'buy_price'  => 'decimal:2',
        'sell_price' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getProfitAttribute()
    {
        return $this->sell_price - $this->buy_price;
    }

    public function getProfitPercentAttribute()
    {
        if ($this->buy_price == 0) return 0;
        return round(($this->profit / $this->buy_price) * 100, 1);
    }

    public function getIsLowStockAttribute()
    {
        return $this->stock <= $this->min_stock;
    }

    public function getImageUrlAttribute()
    {
        if ($this->image && file_exists(public_path('uploads/toys/' . $this->image))) {
            return asset('uploads/toys/' . $this->image);
        }
        return null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('sku', 'like', "%{$term}%")
              ->orWhere('barcode', 'like', "%{$term}%")
              ->orWhere('brand', 'like', "%{$term}%");
        });
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($toy) {
            if (empty($toy->sku)) {
                $toy->sku = 'TOY-' . strtoupper(uniqid());
            }
        });
    }
}
