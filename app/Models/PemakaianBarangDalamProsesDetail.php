<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemakaianBarangDalamProsesDetail extends Model
{
    protected $table = 'pemakaian_barang_dalam_proses_details';
    
    protected $fillable = [
        'pemakaian_barang_dalam_proses_id',
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

    public function pemakaianBarangDalamProses(): BelongsTo
    {
        return $this->belongsTo(PemakaianBarangDalamProses::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
