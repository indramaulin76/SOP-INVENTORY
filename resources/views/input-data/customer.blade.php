@extends('layouts.app')

@section('title', 'Input Data Customer')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>👥 Daftar Data Customer</h2>
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

        <form action="{{ route('input-data.store-customer') }}" method="POST">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label for="kode_customer" class="required">Kode Customer</label>
                    <input type="text" id="kode_customer" name="kode_customer" class="input" value="{{ $kode ?? old('kode_customer', 'CUS001') }}" readonly style="background-color: #f5f5f5;" required>
                    <small style="color: #666; font-style: italic;">Kode customer otomatis dibuat</small>
                </div>
                <div class="form-group">
                    <label for="nama_customer" class="required">Nama Customer</label>
                    <input type="text" id="nama_customer" name="nama_customer" class="input" value="{{ old('nama_customer') }}" placeholder="Nama customer" required>
                    @error('nama_customer') <small style="color: #dc3545;">{{ $message }}</small> @enderror
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
                    <label for="tipe_customer">Tipe Customer</label>
                    <select id="tipe_customer" name="tipe_customer" class="input-select">
                        <option value="">Pilih Tipe</option>
                        <option value="Retail" {{ old('tipe_customer') == 'Retail' ? 'selected' : '' }}>Retail</option>
                        <option value="Wholesale" {{ old('tipe_customer') == 'Wholesale' ? 'selected' : '' }}>Wholesale</option>
                        <option value="Distributor" {{ old('tipe_customer') == 'Distributor' ? 'selected' : '' }}>Distributor</option>
                    </select>
                    @error('tipe_customer') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label for="keterangan">Keterangan</label>
                    <input type="text" id="keterangan" name="keterangan" class="input" value="{{ old('keterangan') }}" placeholder="Keterangan">
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan Customer 💾</button>
            </div>
        </form>
    </div>
</div>
@endsection
