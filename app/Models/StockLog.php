<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLog extends Model
{
    protected $fillable = ['toy_id', 'type', 'qty', 'before', 'after', 'note'];

    public function toy()
    {
        return $this->belongsTo(Toy::class);
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'in'     => '📥',
            'out'    => '📤',
            'adjust' => '⚙️',
            default  => '—',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'in'     => 'Stok Masuk',
            'out'    => 'Stok Keluar',
            'adjust' => 'Penyesuaian',
            default  => $this->type,
        };
    }
}
