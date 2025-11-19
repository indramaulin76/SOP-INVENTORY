@extends('layouts.app')
@section('title', 'Laporan Penjualan')

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

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--gold);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--gold-dark);
        }

        .btn-secondary {
            background: var(--gray-medium);
            color: var(--text-dark);
        }

        .btn-secondary:hover {
            background: var(--gray-dark);
        }

        .btn-danger {
            background: #DC3545;
            color: var(--white);
        }

        .btn-danger:hover {
            background: #C82333;
        }

        .btn-success {
            background: #28A745;
            color: var(--white);
        }

        .btn-success:hover {
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

        .text-right {
            text-align: right;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Laporan Penjualan</h2>
        </div>
        <div class="card-body">
            <div class="filter-container">
                <div class="filter-title">
                    <span>📋</span>
                    <span>Filter Report</span>
                </div>
                <form method="GET" action="{{ route('report.laporan-penjualan') }}">
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
                            <label>Customer</label>
                            <input type="text" name="customer" value="{{ request('customer') }}" placeholder="Cari customer...">
                        </div>
                        <div class="filter-group">
                            <label>Item</label>
                            <select name="item">
                                <option value="">-- Semua Item --</option>
                                @forelse($barangs ?? [] as $barang)
                                    <option value="{{ $barang->id }}" {{ request('item') && (int)request('item') == $barang->id ? 'selected' : '' }}>
                                        {{ $barang->kode_barang }} - {{ $barang->nama_barang }}
                                    </option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Nomor Bukti</label>
                            <input type="text" name="nomor_bukti" value="{{ request('nomor_bukti') }}" placeholder="Cari nomor bukti...">
                        </div>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <span>🔍</span>
                            <span>Terapkan Filter</span>
                        </button>
                        <a href="{{ route('report.laporan-penjualan') }}" class="btn btn-secondary">
                            <span>🔄</span>
                            <span>Reset</span>
                        </a>
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
                            <th>Nomor Bukti</th>
                            <th>Keterangan</th>
                            <th>Customer</th>
                            <th>Kode Customer</th>
                            <th>Item</th>
                            <th>Kode Item</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data ?? [] as $item)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y') }}</td>
                                <td>{{ $item['nomor_bukti'] }}</td>
                                <td>{{ $item['keterangan'] ?? '-' }}</td>
                                <td>{{ $item['customer'] }}</td>
                                <td>{{ $item['kode_customer'] }}</td>
                                <td>{{ $item['item'] }}</td>
                                <td>{{ $item['kode_item'] }}</td>
                                <td class="text-right">{{ number_format($item['qty'], 2, ',', '.') }}</td>
                                <td>{{ $item['unit'] }}</td>
                                <td class="text-right">Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($item['jumlah'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" style="text-align: center; color: #999; padding: 40px;">Tidak ada data tersedia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="table-footer">
                    <span class="total-label">Total:</span>
                    <span class="total-amount">Rp {{ number_format($total ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function exportPDF(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            const currentParams = new URLSearchParams(window.location.search);
            currentParams.set('export', 'pdf');
            const newUrl = window.location.pathname + '?' + currentParams.toString();
            
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = newUrl;
            document.body.appendChild(iframe);
            setTimeout(function() {
                document.body.removeChild(iframe);
            }, 2000);
            
            return false;
        }

        function exportExcel(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            const currentParams = new URLSearchParams(window.location.search);
            currentParams.set('export', 'excel');
            const newUrl = window.location.pathname + '?' + currentParams.toString();
            
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = newUrl;
            document.body.appendChild(iframe);
            setTimeout(function() {
                document.body.removeChild(iframe);
            }, 2000);
            
            return false;
        }
    </script>
@endsection
