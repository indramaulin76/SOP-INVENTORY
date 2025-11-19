<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PembelianBahanBaku extends Model
{
    protected $table = 'pembelian_bahan_bakus';
    
    protected $fillable = [
        'tanggal',
        'nomor_faktur',
        'keterangan',
        'supplier_id',
        'total_harga',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_harga' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(PembelianBahanBakuDetail::class);
    }
}
