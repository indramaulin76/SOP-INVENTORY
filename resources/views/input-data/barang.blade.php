@extends('layouts.app')

@section('title', 'Input Data Barang')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Input Data Barang</h2>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('input-data.store-barang') }}" method="POST">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="nama_barang" class="required">Nama Barang</label>
                    <input type="text" id="nama_barang" name="nama_barang" class="input" value="{{ old('nama_barang') }}" placeholder="Nama barang" required>
                    @error('nama_barang') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                    <small style="color: #666; font-style: italic;">Kode barang akan otomatis dibuat (KODE-001, KODE-002, dst.)</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="kategori" class="required">Kategori Barang</label>
                    <select id="kategori" name="kategori" class="input-select" required>
                        <option value="">Pilih Kategori</option>
                        <option value="bahan_baku" {{ old('kategori') == 'bahan_baku' ? 'selected' : '' }}>Bahan Baku</option>
                        <option value="barang_jadi" {{ old('kategori') == 'barang_jadi' ? 'selected' : '' }}>Barang Jadi</option>
                        <option value="barang_proses" {{ old('kategori') == 'barang_proses' ? 'selected' : '' }}>Barang Dalam Proses</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="limit_stock" class="required">Limit Stock</label>
                    <input type="number" id="limit_stock" name="limit_stock" class="input" value="{{ old('limit_stock', 0) }}" placeholder="0" min="0" step="1" required>
                    @error('limit_stock') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="satuan" class="required">Jenis Satuan</label>
                    <select id="satuan" name="satuan" class="input-select" required>
                        <option value="">Pilih Satuan</option>
                        <option value="Gram" {{ old('satuan') == 'Gram' ? 'selected' : '' }}>Gram (g)</option>
                        <option value="Kilogram" {{ old('satuan') == 'Kilogram' ? 'selected' : '' }}>Kilogram (kg)</option>
                        <option value="Milliliter" {{ old('satuan') == 'Milliliter' ? 'selected' : '' }}>Milliliter (ml)</option>
                        <option value="Pcs" {{ old('satuan') == 'Pcs' ? 'selected' : '' }}>Pcs (piece)</option>
                        <option value="Pack" {{ old('satuan') == 'Pack' ? 'selected' : '' }}>Pack</option>
                    </select>
                    @error('satuan') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan Barang 💾</button>
            </div>
        </form>
    </div>
</div>
@endsection
