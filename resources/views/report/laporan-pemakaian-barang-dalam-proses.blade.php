@extends('layouts.app')
@section('title', 'Laporan Pemakaian Barang Dalam Proses')

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

        .btn-delete {
            background: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.3s;
        }

        .btn-delete:hover {
            background: #c82333;
        }

        .action-cell {
            text-align: center;
            white-space: nowrap;
        }
    </style>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom: 20px; padding: 15px; background: #d4edda; color: #155724; border-radius: 5px;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" style="margin-bottom: 20px; padding: 15px; background: #f8d7da; color: #721c24; border-radius: 5px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h2>Laporan Pemakaian Barang Dalam Proses</h2>
        </div>
        <div class="card-body">
            <div class="filter-container">
                <div class="filter-title">
                    <span>📋</span>
                    <span>Filter Report</span>
                </div>
                <form method="GET" action="{{ route('report.laporan-pemakaian-barang-dalam-proses') }}">
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
                            <label>Departemen/Produksi</label>
                            <input type="text" name="customer" value="{{ request('customer') }}" placeholder="Cari departemen...">
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
                        <a href="{{ route('report.laporan-pemakaian-barang-dalam-proses') }}" class="btn btn-secondary">
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
                            <th>Departemen/Produksi</th>
                            <th>Barang</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                            <th>Harga</th>
                            <th>Total</th>
                            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'superadmin']))
                                <th class="action-cell">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data ?? [] as $item)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y') }}</td>
                                <td>{{ $item['nomor_bukti'] }}</td>
                                <td>{{ $item['departemen'] }}</td>
                                <td>{{ $item['barang'] }}</td>
                                <td class="text-right">{{ number_format($item['qty'], 2, ',', '.') }}</td>
                                <td>{{ $item['satuan'] }}</td>
                                <td class="text-right">Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                                @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'superadmin']))
                                    <td class="action-cell">
                                        <form action="{{ route('report.destroy-pemakaian-barang-dalam-proses', $item['pemakaian_id']) }}" method="POST" style="display: inline;" onsubmit="return confirmDelete('{{ $item['nomor_bukti'] }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete" title="Hapus data pemakaian">
                                                🗑️
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->check() && in_array(auth()->user()->role, ['admin', 'superadmin']) ? '9' : '8' }}" style="text-align: center; color: #999; padding: 40px;">Tidak ada data tersedia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="table-footer">
                    <span class="total-label">Total Pemakaian:</span>
                    <span class="total-amount">Rp {{ number_format($total ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function confirmDelete(nomorBukti) {
            return confirm('Apakah Anda yakin ingin menghapus data pemakaian dengan nomor bukti "' + nomorBukti + '"?\n\nTindakan ini akan menambah kembali stok barang dan tidak dapat dibatalkan!');
        }
    </script>
@endsection
