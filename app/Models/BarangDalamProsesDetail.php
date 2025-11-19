<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangDalamProsesDetail extends Model
{
    protected $table = 'barang_dalam_proses_details';
    
    protected $fillable = [
        'barang_dalam_proses_id',
        'barang_nama',
        'barang_kode',
        'barang_qty',
        'barang_satuan',
        'barang_harga',
        'barang_jumlah',
    ];

    protected $casts = [
        'barang_qty' => 'decimal:2',
        'barang_harga' => 'decimal:2',
        'barang_jumlah' => 'decimal:2',
    ];

    public function barangDalamProses(): BelongsTo
    {
        return $this->belongsTo(BarangDalamProses::class);
    }
}
