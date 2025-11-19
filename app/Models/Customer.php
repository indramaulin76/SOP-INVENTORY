<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    
    protected $fillable = [
        'kode_customer',
        'nama_customer',
        'alamat',
        'telepon',
        'email',
        'tipe_customer',
        'keterangan',
    ];
}
