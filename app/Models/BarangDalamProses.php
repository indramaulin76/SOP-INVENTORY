<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarangDalamProses extends Model
{
    protected $table = 'barang_dalam_proses';
    
    protected $fillable = [
        'tanggal',
        'nomor_faktur',
        'keterangan',
        'nama_supplier',
        'kode_supplier',
        'total_harga',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_harga' => 'decimal:2',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(BarangDalamProsesDetail::class);
    }
}
