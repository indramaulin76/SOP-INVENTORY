<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PemakaianBahanBakuRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tanggal' => 'required|date',
            'nomor_bukti' => 'required|string|max:255|unique:pemakaian_bahan_bakus,nomor_bukti',
            'keterangan' => 'nullable|string',
            'nama_customer' => 'required|string|max:255|exists:customers,nama_customer', // Nama Departemen/Produksi - harus ada di master customer
            'kode_customer' => 'required|string|max:255|exists:customers,kode_customer', // Kode Referensi - harus sesuai dengan nama customer

            // VALIDASI Barang Baku
            'barang_nama' => 'required|array',
            'barang_nama.*' => 'required|exists:barangs,nama_barang',
            'barang_kode' => 'required|array',
            'barang_kode.*' => 'required|exists:barangs,kode_barang',
            'barang_qty' => 'required|array',
            'barang_qty.*' => 'required|numeric|min:0.01',
            'barang_satuan' => 'required|array',
            'barang_satuan.*' => 'required|string',
            'barang_harga' => 'required|array',
            'barang_harga.*' => 'required|numeric|min:0',
            'barang_jumlah' => 'required|array',
            'barang_jumlah.*' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'nomor_bukti.required' => 'Nomor bukti wajib diisi.',
            'nomor_bukti.unique' => 'Nomor bukti sudah digunakan.',
            'nama_customer.required' => 'Nama departemen/produksi wajib diisi.',
            'nama_customer.exists' => 'Nama customer tidak ditemukan di master data.',
            'kode_customer.required' => 'Kode referensi wajib diisi.',
            'kode_customer.exists' => 'Kode customer tidak valid.',
            'barang_nama.required' => 'Minimal satu barang harus diinput.',
            'barang_nama.*.required' => 'Nama barang wajib diisi.',
            'barang_nama.*.exists' => 'Nama bahan baku tidak terdaftar dalam master barang.',
            'barang_kode.required' => 'Minimal satu barang harus diinput.',
            'barang_kode.*.required' => 'Kode barang wajib diisi.',
            'barang_kode.*.exists' => 'Kode bahan baku tidak sesuai master barang.',
            'barang_qty.*.required' => 'Quantity wajib diisi.',
            'barang_qty.*.min' => 'Quantity harus lebih dari 0.',
            'barang_satuan.*.required' => 'Satuan wajib dipilih.',
            'barang_harga.*.required' => 'Harga wajib diisi.',
            'barang_harga.*.min' => 'Harga tidak boleh kurang dari 0.',
            'barang_jumlah.*.required' => 'Jumlah wajib diisi.',
        ];
    }
}

