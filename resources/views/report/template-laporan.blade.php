@extends('layouts.app')
@section('title', $title ?? 'Laporan')

@section('styles')
    <style>
        .filter-container {
            background: var(--white);
            padding: 20px;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            margin-bottom: 20px;
        }

        .filter-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 15px;
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--text-dark);
            font-size: 13px;
        }

        .filter-group input,
        .filter-group select {
            padding: 8px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 13px;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 15px;
        }

        .btn-export {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-export-pdf {
            background: #DC3545;
            color: var(--white);
        }

        .btn-export-pdf:hover {
            background: #C82333;
        }

        .btn-export-excel {
            background: #28A745;
            color: var(--white);
        }

        .btn-export-excel:hover {
            background: #218838;
        }

        .table-container {
            background: var(--white);
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            overflow: auto;
        }

        .table-container table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-container thead {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: var(--white);
        }

        .table-container th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            border-bottom: 2px solid var(--gold-dark);
        }

        .table-container td {
            padding: 12px;
            border-bottom: 1px solid var(--border-color);
            font-size: 13px;
        }

        .table-container tbody tr:hover {
            background-color: var(--cream);
        }

        .table-footer {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 15px 20px;
            background-color: var(--gray-light);
            border-top: 2px solid var(--border-color);
            gap: 15px;
            font-weight: 600;
        }

        .total-label {
            color: var(--text-dark);
        }

        .total-amount {
            color: var(--gold);
            font-size: 16px;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>{{ $title }}</h2>
        </div>
        <div class="card-body">
            <div class="filter-container">
                <div class="filter-title">📋 Filter Report</div>
                <form method="GET" action="">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}">
                        </div>
                        <div class="filter-group">
                            <label>Tanggal Akhir</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}">
                        </div>
                    </div>

                    <div class="filter-row">
                        <div class="filter-group">
                            <label>Supplier</label>
                            <select name="supplier">
                                <option value="">-- Semua Supplier --</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Item</label>
                            <select name="item">
                                <option value="">-- Semua Item --</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Nomor Invoice</label>
                            <input type="text" name="invoice_number" value="{{ request('invoice_number') }}" placeholder="Cari invoice...">
                        </div>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn-export btn-export-pdf">
                            📄 Export PDF
                        </button>
                        <button type="button" class="btn-export btn-export-excel" onclick="exportExcel()">
                            📊 Export Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card mt-20">
        <div class="card-body">
            <div class="table-container">
                <table>
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
                            <td colspan="11" style="text-align: center; color: #999; padding: 40px;">Tidak ada data tersedia</td>
                        </tr>
                    </tbody>
                </table>
                <div class="table-footer">
                    <span class="total-label">Total:</span>
                    <span class="total-amount">Rp0</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function exportExcel() {
            alert('Export Excel akan diimplementasikan');
        }
    </script>
@endsection
