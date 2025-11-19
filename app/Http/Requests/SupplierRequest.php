<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $supplierId = $this->route('id'); // For update, if needed
        
        return [
            'kode_supplier' => [
                'required',
                'string',
                'max:255',
                Rule::unique('suppliers', 'kode_supplier')->ignore($supplierId)
            ],
            'nama_supplier' => [
                'required',
                'string',
                'max:255',
                Rule::unique('suppliers', 'nama_supplier')->ignore($supplierId)
            ],
            'alamat' => 'required|string|max:500',
            'telepon' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'nama_pemilik' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'kode_supplier.required' => 'Kode supplier wajib diisi.',
            'kode_supplier.unique' => 'Kode supplier sudah digunakan. Silakan gunakan kode yang berbeda.',
            'kode_supplier.max' => 'Kode supplier maksimal 255 karakter.',
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
            'nama_supplier.unique' => 'Nama supplier sudah terdaftar.',
            'nama_supplier.max' => 'Nama supplier maksimal 255 karakter.',
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.max' => 'Alamat maksimal 500 karakter.',
            'telepon.required' => 'Nomor telepon wajib diisi.',
            'telepon.max' => 'Nomor telepon maksimal 20 karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
        ];
    }
}

