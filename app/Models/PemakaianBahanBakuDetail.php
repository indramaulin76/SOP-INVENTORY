<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemakaianBahanBakuDetail extends Model
{
    protected $table = 'pemakaian_bahan_baku_details';
    
    protected $fillable = [
        'pemakaian_bahan_baku_id',
        'barang_id',
        'barang_nama',
        'barang_kode',
        'quantity',
        'satuan',
        'harga',
        'jumlah',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'harga' => 'decimal:2',
        'jumlah' => 'decimal:2',
    ];

    public function pemakaianBahanBaku(): BelongsTo
    {
        return $this->belongsTo(PemakaianBahanBaku::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
