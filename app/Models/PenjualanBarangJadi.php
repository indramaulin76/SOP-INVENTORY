<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenjualanBarangJadi extends Model
{
    protected $table = 'penjualan_barang_jadi';
    
    protected $fillable = [
        'tanggal',
        'nomor_bukti',
        'keterangan',
        'nama_customer',
        'kode_customer',
        'total_harga',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_harga' => 'decimal:2',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(PenjualanBarangJadiDetail::class);
    }
}
