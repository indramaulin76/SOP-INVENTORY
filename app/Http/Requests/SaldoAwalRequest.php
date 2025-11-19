<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaldoAwalRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'barang_nama' => 'required|array',
            'barang_nama.*' => [
                'required',
                'string',
                Rule::exists('barangs', 'nama_barang')
            ],
            'barang_kode' => 'required|array',
            'barang_kode.*' => [
                'required',
                'string',
                Rule::exists('barangs', 'kode_barang')
            ],
            'barang_kategori' => 'required|array',
            'barang_kategori.*' => 'required|string|in:bahan_baku,barang_jadi,barang_proses',
            'barang_qty' => 'required|array',
            'barang_qty.*' => 'required|numeric|min:0',
            'barang_satuan' => 'required|array',
            'barang_satuan.*' => 'required|string|in:Gram,Kilogram,Milliliter,Pcs,Pack',
            'barang_harga' => 'required|array',
            'barang_harga.*' => 'required|numeric|min:0',
            'barang_harga_jual' => 'required|array',
            'barang_harga_jual.*' => 'required|numeric|min:0',
            'barang_jumlah' => 'required|array',
            'barang_jumlah.*' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'barang_nama.required' => 'Minimal satu barang harus diinput.',
            'barang_nama.*.required' => 'Nama barang wajib diisi.',
            'barang_nama.*.exists' => 'Nama barang :input tidak ditemukan di master data. Pastikan barang sudah terdaftar.',
            'barang_kode.required' => 'Minimal satu barang harus diinput.',
            'barang_kode.*.required' => 'Kode barang wajib diisi.',
            'barang_kode.*.exists' => 'Kode barang :input tidak ditemukan di master data.',
            'barang_kategori.*.required' => 'Kategori barang wajib dipilih.',
            'barang_kategori.*.in' => 'Kategori barang tidak valid.',
            'barang_qty.*.required' => 'Jumlah unit wajib diisi.',
            'barang_qty.*.min' => 'Jumlah unit tidak boleh kurang dari 0.',
            'barang_satuan.*.required' => 'Jenis satuan wajib dipilih.',
            'barang_satuan.*.in' => 'Jenis satuan tidak valid.',
            'barang_harga.*.required' => 'Harga beli wajib diisi.',
            'barang_harga.*.min' => 'Harga beli tidak boleh kurang dari 0.',
            'barang_harga_jual.*.required' => 'Harga jual wajib diisi.',
            'barang_harga_jual.*.min' => 'Harga jual tidak boleh kurang dari 0.',
        ];
    }
}

