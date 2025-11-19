@extends('layouts.app')

@section('title', 'Input Data Supplier')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>📋 Daftar Data Supplier</h2>
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

        <form action="{{ route('input-data.store-supplier') }}" method="POST">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label for="kode_supplier" class="required">Kode Supplier</label>
                    <input type="text" id="kode_supplier" name="kode_supplier" class="input" value="{{ $newCode ?? 'SUP001' }}" readonly style="background-color: #f5f5f5;" required>
                    <small style="color: #666; font-style: italic;">Kode supplier otomatis dibuat</small>
                </div>
                <div class="form-group">
                    <label for="nama_supplier" class="required">Nama Supplier</label>
                    <input type="text" id="nama_supplier" name="nama_supplier" class="input" value="{{ old('nama_supplier') }}" placeholder="Nama supplier" required>
                    @error('nama_supplier') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label for="alamat" class="required">Alamat</label>
                    <input type="text" id="alamat" name="alamat" class="input" value="{{ old('alamat') }}" placeholder="Alamat" required>
                    @error('alamat') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label for="telepon" class="required">Telepon</label>
                    <input type="text" id="telepon" name="telepon" class="input" value="{{ old('telepon') }}" placeholder="Nomor telepon" required>
                    @error('telepon') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="input" value="{{ old('email') }}" placeholder="Email">
                    @error('email') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label for="nama_pemilik">Nama Pemilik</label>
                    <input type="text" id="nama_pemilik" name="nama_pemilik" class="input" value="{{ old('nama_pemilik') }}" placeholder="Nama pemilik">
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label for="keterangan">Keterangan</label>
                    <input type="text" id="keterangan" name="keterangan" class="input" value="{{ old('keterangan') }}" placeholder="Keterangan">
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan Supplier 💾</button>
            </div>
        </form>
    </div>
</div>
@endsection
