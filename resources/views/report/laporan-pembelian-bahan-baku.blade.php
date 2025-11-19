@extends('report.template-laporan', ['title' => 'Laporan Pembelian Bahan Baku'])

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
            <h2>Laporan Pembelian Bahan Baku</h2>
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
                                @forelse($suppliers ?? [] as $supplier)
                                    <option value="{{ $supplier->id }}" {{ request('supplier') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->nama_supplier }}
                                    </option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Nomor Invoice</label>
                            <input type="text" name="invoice_number" value="{{ request('invoice_number') }}" placeholder="Cari invoice...">
                        </div>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">🔍 Cari</button>
                        <button type="button" class="btn btn-danger" onclick="exportPDF(); return false;">
                            <span>📄</span>
                            <span>Export PDF</span>
                        </button>
                        <button type="button" class="btn btn-success" onclick="exportExcel(); return false;">
                            <span>📊</span>
                            <span>Export Excel</span>
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
                            <th>Nomor Faktur</th>
                            <th>Supplier</th>
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
                        @forelse($data ?? [] as $row)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                                <td>{{ $row->nomor_faktur }}</td>
                                <td>{{ $row->supplier->nama_supplier ?? '-' }}</td>
                                <td>
                                    @forelse($row->details as $detail)
                                        <div>{{ $detail->barang->nama_barang }}</div>
                                    @empty
                                        -
                                    @endforelse
                                </td>
                                <td>
                                    @forelse($row->details as $detail)
                                        <div>{{ $detail->quantity }}</div>
                                    @empty
                                        -
                                    @endforelse
                                </td>
                                <td>
                                    @forelse($row->details as $detail)
                                        <div>{{ $detail->satuan }}</div>
                                    @empty
                                        -
                                    @endforelse
                                </td>
                                <td>
                                    @forelse($row->details as $detail)
                                        <div>Rp{{ number_format($detail->harga_beli ?? 0, 0, ',', '.') }}</div>
                                    @empty
                                        -
                                    @endforelse
                                </td>
                                <td class="text-right">Rp{{ number_format($row->total_harga, 0, ',', '.') }}</td>
                                @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'superadmin']))
                                    <td class="action-cell">
                                        <form action="{{ route('report.destroy-pembelian-bahan-baku', $row->id) }}" method="POST" style="display: inline;" onsubmit="return confirmDelete('{{ $row->nomor_faktur }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete" title="Hapus data pembelian">
                                                🗑️ Hapus
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
                    <span class="total-label">Total Pembelian:</span>
                    <span class="total-amount">Rp{{ number_format($total ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <style>
        .btn-delete {
            background: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
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
    <script>
        function confirmDelete(nomorFaktur) {
            return confirm('Apakah Anda yakin ingin menghapus data pembelian dengan nomor faktur "' + nomorFaktur + '"?\n\nTindakan ini akan mengurangi stok barang dan tidak dapat dibatalkan!');
        }

        function exportPDF(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            // Get all current URL parameters
            const currentParams = new URLSearchParams(window.location.search);
            currentParams.set('export', 'pdf');
            
            // Build new URL preserving all existing parameters
            const newUrl = window.location.pathname + '?' + currentParams.toString();
            
            // Use hidden iframe to download without affecting current page
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = newUrl;
            document.body.appendChild(iframe);
            
            // Remove iframe after a delay
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
            
            // Get all current URL parameters
            const currentParams = new URLSearchParams(window.location.search);
            currentParams.set('export', 'excel');
            
            // Build new URL preserving all existing parameters
            const newUrl = window.location.pathname + '?' + currentParams.toString();
            
            // Use hidden iframe to download without affecting current page
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = newUrl;
            document.body.appendChild(iframe);
            
            // Remove iframe after a delay
            setTimeout(function() {
                document.body.removeChild(iframe);
            }, 2000);
            
            return false;
        }
    </script>
@endsection
