@extends('layouts.app')
@section('title', 'Laporan Data Barang')

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

        .btn-filter {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            background: var(--gold);
            color: var(--white);
        }

        .btn-filter:hover {
            background: var(--gold-dark);
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
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: var(--gray-light);
            border-top: 2px solid var(--border-color);
            font-weight: 600;
        }

        .total-label {
            color: var(--text-dark);
        }

        .total-amount {
            color: var(--gold);
            font-size: 16px;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-bahan-baku {
            background: #E3F2FD;
            color: #1976D2;
        }

        .badge-barang-jadi {
            background: #E8F5E9;
            color: #388E3C;
        }

        .badge-barang-proses {
            background: #FFF3E0;
            color: #F57C00;
        }

        .btn-delete {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            background: #DC3545;
            color: var(--white);
        }

        .btn-delete:hover {
            background: #C82333;
        }

        .action-cell {
            text-align: center;
            width: 100px;
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
            <h2>Laporan Data Barang</h2>
        </div>
        <div class="card-body">
            <div class="filter-container">
                <div class="filter-title">📋 Filter Report</div>
                <form method="GET" action="{{ route('report.laporan-data-barang') }}">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label>Kategori</label>
                            <select name="kategori">
                                <option value="">-- Semua Kategori --</option>
                                <option value="bahan_baku" {{ request('kategori') == 'bahan_baku' ? 'selected' : '' }}>Bahan Baku</option>
                                <option value="barang_jadi" {{ request('kategori') == 'barang_jadi' ? 'selected' : '' }}>Barang Jadi</option>
                                <option value="barang_proses" {{ request('kategori') == 'barang_proses' ? 'selected' : '' }}>Barang Dalam Proses</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Cari (Nama/Kode Barang)</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode barang...">
                        </div>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn-filter">
                            🔍 Filter
                        </button>
                        <a href="{{ route('report.laporan-data-barang') }}" class="btn-export btn-export-pdf" style="text-decoration: none; display: inline-block;">
                            🔄 Reset
                        </a>
                        <button type="button" class="btn-export btn-export-pdf" onclick="exportPDF()">
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
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Satuan</th>
                            <th>Stok</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'superadmin']))
                                <th class="action-cell">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $barang)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $barang->kode_barang }}</strong></td>
                                <td>{{ $barang->nama_barang }}</td>
                                <td>
                                    @php
                                        $kategori = $barang->deskripsi ?? '';
                                        $badgeClass = 'badge-bahan-baku';
                                        $kategoriLabel = 'Bahan Baku';
                                        
                                        if (str_contains($kategori, 'barang_jadi')) {
                                            $badgeClass = 'badge-barang-jadi';
                                            $kategoriLabel = 'Barang Jadi';
                                        } elseif (str_contains($kategori, 'barang_proses')) {
                                            $badgeClass = 'badge-barang-proses';
                                            $kategoriLabel = 'Barang Dalam Proses';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $kategoriLabel }}</span>
                                </td>
                                <td>{{ strtoupper($barang->satuan) }}</td>
                                <td>{{ number_format($barang->stok, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($barang->harga_beli ?? 0, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($barang->harga_jual ?? 0, 0, ',', '.') }}</td>
                                @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'superadmin']))
                                    <td class="action-cell">
                                        <form action="{{ route('input-data.destroy-barang', $barang->id) }}" method="POST" style="display: inline;" onsubmit="return confirmDelete('{{ $barang->nama_barang }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete" title="Hapus data barang">
                                                🗑️ Hapus
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->check() && in_array(auth()->user()->role, ['admin', 'superadmin']) ? '9' : '8' }}" style="text-align: center; color: #999; padding: 40px;">
                                    Tidak ada data barang tersedia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="table-footer">
                    <span class="total-label">Total Barang: <strong>{{ $data->count() }}</strong></span>
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

        function confirmDelete(namaBarang) {
            return confirm('Apakah Anda yakin ingin menghapus data barang "' + namaBarang + '"?\n\nTindakan ini tidak dapat dibatalkan!');
        }
    </script>
@endsection
