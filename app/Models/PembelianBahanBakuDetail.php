<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembelianBahanBakuDetail extends Model
{
    protected $table = 'pembelian_bahan_baku_details';
    
    protected $fillable = [
        'pembelian_bahan_baku_id',
        'barang_id',
        'quantity',
        'satuan',
        'harga_beli',
        'jumlah',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'harga_beli' => 'decimal:2',
        'jumlah' => 'decimal:2',
    ];

    public function pembelianBahanBaku(): BelongsTo
    {
        return $this->belongsTo(PembelianBahanBaku::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
