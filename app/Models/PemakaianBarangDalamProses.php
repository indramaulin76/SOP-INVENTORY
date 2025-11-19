<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PemakaianBarangDalamProses extends Model
{
    protected $table = 'pemakaian_barang_dalam_proses';
    
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
        return $this->hasMany(PemakaianBarangDalamProsesDetail::class);
    }
}
