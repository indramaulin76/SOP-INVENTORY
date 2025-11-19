<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PenjualanBarangJadiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tanggal' => 'required|date',
            'nomor_bukti' => 'required|string|max:255',
            'keterangan' => 'nullable|string',

            // Nama customer harus ada di tabel customers
            'nama_customer' => 'required|exists:customers,nama_customer',

            // Kode customer harus sesuai dengan nama customer
            'kode_customer' => 'required|exists:customers,kode_customer',

            // Detail barang
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
            'nama_customer.required' => 'Nama customer wajib diisi.',
            'nama_customer.exists' => 'Nama customer tidak ditemukan di master data.',
            'kode_customer.required' => 'Kode customer wajib diisi.',
            'kode_customer.exists' => 'Kode customer tidak valid.',
            'barang_nama.required' => 'Minimal satu barang harus diinput.',
            'barang_nama.*.required' => 'Nama barang wajib diisi.',
            'barang_nama.*.exists' => 'Nama barang tidak ada di master data.',
            'barang_kode.required' => 'Minimal satu barang harus diinput.',
            'barang_kode.*.required' => 'Kode barang wajib diisi.',
            'barang_kode.*.exists' => 'Kode barang tidak ada di master data.',
            'barang_qty.*.required' => 'Quantity wajib diisi.',
            'barang_qty.*.min' => 'Quantity harus lebih dari 0.',
            'barang_satuan.*.required' => 'Satuan wajib dipilih.',
            'barang_harga.*.required' => 'Harga jual wajib diisi.',
            'barang_harga.*.min' => 'Harga jual tidak boleh kurang dari 0.',
            'barang_jumlah.*.required' => 'Jumlah wajib diisi.',
        ];
    }
}

