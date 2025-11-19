<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $table = 'barangs';
    
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'deskripsi',
        'satuan',
        'stok',
        'harga_beli',
        'harga_jual',
    ];

    protected $casts = [
        'stok' => 'integer',
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
    ];

    /**
     * Get pembelian bahan baku details for this barang
     */
    public function pembelianDetails(): HasMany
    {
        return $this->hasMany(\App\Models\PembelianBahanBakuDetail::class);
    }

    /**
     * Update stock (increment)
     */
    public function addStock(float $quantity): void
    {
        $this->increment('stok', $quantity);
    }

    /**
     * Update stock (decrement)
     */
    public function reduceStock(float $quantity): void
    {
        $this->decrement('stok', $quantity);
    }

    /**
     * Check if stock is available
     */
    public function hasStock(float $quantity): bool
    {
        return $this->stok >= $quantity;
    }
}
