<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $customerId = $this->route('id'); // For update, if needed
        
        return [
            'nama_customer' => [
                'required',
                'string',
                'max:255',
                Rule::unique('customers', 'nama_customer')->ignore($customerId)
            ],
            'alamat' => 'required|string|max:500',
            'telepon' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'tipe_customer' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'nama_customer.required' => 'Nama customer wajib diisi.',
            'nama_customer.unique' => 'Nama customer sudah terdaftar.',
            'nama_customer.max' => 'Nama customer maksimal 255 karakter.',
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.max' => 'Alamat maksimal 500 karakter.',
            'telepon.required' => 'Nomor telepon wajib diisi.',
            'telepon.max' => 'Nomor telepon maksimal 20 karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
        ];
    }
}

