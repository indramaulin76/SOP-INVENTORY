<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenjualanBarangJadiDetail extends Model
{
    protected $table = 'penjualan_barang_jadi_details';
    
    protected $fillable = [
        'penjualan_barang_jadi_id',
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

    public function penjualanBarangJadi(): BelongsTo
    {
        return $this->belongsTo(PenjualanBarangJadi::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
