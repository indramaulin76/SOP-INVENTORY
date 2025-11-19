@extends('layouts.app')
@section('title', 'Laporan')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>📊 Filter Laporan</h2>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('report.index') }}" class="form-grid">
                <div class="form-group">
                    <label for="start_date">Tanggal Mulai</label>
                    <input type="date" id="start_date" name="start_date" class="input">
                </div>
                <div class="form-group">
                    <label for="end_date">Tanggal Akhir</label>
                    <input type="date" id="end_date" name="end_date" class="input">
                </div>
                <div class="form-group">
                    <label for="supplier">Supplier</label>
                    <select id="supplier" name="supplier" class="input-select">
                        <option>-- Semua Supplier --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="item">Item</label>
                    <select id="item" name="item" class="input-select">
                        <option>-- Semua Item --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="invoice_number">Nomor Invoice</label>
                    <input type="text" id="invoice_number" name="invoice_number" class="input" placeholder="Cari invoice...">
                </div>
            </form>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-danger" onclick="alert('Export PDF')">📄 Export PDF</button>
            <button type="submit" class="btn btn-success" onclick="alert('Export Excel')">📊 Export Excel</button>
        </div>
    </div>

    <div class="card mt-20">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-gold table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nomor Invoice</th>
                            <th>Deskripsi</th>
                            <th>Supplier</th>
                            <th>Kode Supplier</th>
                            <th>Item</th>
                            <th>Kode Item</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="11" class="text-center">Tidak ada data</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="text-bold">
                            <td colspan="10" class="text-right">Total:</td>
                            <td>Rp0</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
