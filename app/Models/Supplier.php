<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $table = 'suppliers';
    
    protected $fillable = [
        'kode_supplier',
        'nama_supplier',
        'alamat',
        'telepon',
        'email',
        'nama_pemilik',
        'keterangan',
    ];

    public function pembelianBahanBaku(): HasMany
    {
        return $this->hasMany(PembelianBahanBaku::class);
    }
}
