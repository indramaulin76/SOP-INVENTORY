<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryBatch extends Model
{
    protected $fillable = [
        'barang_id',
        'tanggal_masuk',
        'qty_awal',
        'qty_sisa',
        'harga_beli',
        'sumber',
        'source_type',
        'source_id'
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'qty_awal' => 'decimal:2',
        'qty_sisa' => 'decimal:2',
        'harga_beli' => 'decimal:2',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}
