@extends('report.template-laporan', ['title' => 'Laporan Barang Dalam Proses'])

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Laporan Barang Dalam Proses</h2>
        </div>
        <div class="card-body">
            <div class="filter-container">
                <div class="filter-title">
                    <span>📋</span>
                    <span>Filter Report</span>
                </div>
                <form method="GET" action="">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" placeholder="mm/dd/yyyy">
                        </div>
                        <div class="filter-group">
                            <label>Tanggal Akhir</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" placeholder="mm/dd/yyyy">
                        </div>
                    </div>

                    <div class="filter-row">
                        <div class="filter-group">
                            <label>Nomor Invoice</label>
                            <input type="text" name="invoice_number" value="{{ request('invoice_number') }}" placeholder="Cari invoice...">
                        </div>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <span>🔍</span>
                            <span>Cari</span>
                        </button>
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
                            <th>Barang</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                            <th>Harga</th>
                            <th>Total</th>
                            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'superadmin']))
                                <th style="width: 50px;">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $processedFaktur = [];
                        @endphp
                        @forelse($data ?? [] as $index => $row)
                            @php
                                $isFirstRow = !in_array($row['nomor_faktur'] ?? '', $processedFaktur);
                                if ($isFirstRow) {
                                    $processedFaktur[] = $row['nomor_faktur'] ?? '';
                                }
                            @endphp
                            <tr>
                                <td>
                                    @if(is_string($row['tanggal']))
                                        {{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') }}
                                    @elseif(is_object($row['tanggal']) && method_exists($row['tanggal'], 'format'))
                                        {{ $row['tanggal']->format('d/m/Y') }}
                                    @else
                                        {{ $row['tanggal'] ?? '-' }}
                                    @endif
                                </td>
                                <td>{{ $row['nomor_faktur'] ?? '-' }}</td>
                                <td>{{ $row['barang'] ?? '-' }}</td>
                                <td>{{ number_format($row['qty'] ?? 0, 2, ',', '.') }}</td>
                                <td>{{ $row['satuan'] ?? '-' }}</td>
                                <td>Rp{{ number_format($row['harga'] ?? 0, 0, ',', '.') }}</td>
                                <td class="text-right">Rp{{ number_format($row['total'] ?? 0, 0, ',', '.') }}</td>
                                @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'superadmin']))
                                    <td>
                                        @if($isFirstRow && isset($row['transaksi_id']))
                                            <button 
                                                type="button" 
                                                class="btn-delete" 
                                                onclick="deleteBarangDalamProses({{ $row['transaksi_id'] ?? 0 }})"
                                                title="Hapus Transaksi">
                                                🗑️
                                            </button>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->check() && in_array(auth()->user()->role, ['admin', 'superadmin']) ? '8' : '7' }}" style="text-align: center; color: #999; padding: 40px;">Tidak ada data tersedia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="table-footer">
                    <span class="total-label">Total Barang Dalam Proses:</span>
                    <span class="total-amount">Rp{{ number_format($total ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    /* Filter Container Styling */
    .filter-container {
        background: #f5f5f5;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .filter-title {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
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
        color: #333;
        font-size: 13px;
    }

    .filter-group input,
    .filter-group select {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 13px;
        background: white;
        transition: all 0.3s ease;
    }

    .filter-group input:focus,
    .filter-group select:focus {
        outline: none;
        border-color: #D4AF37;
        box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-primary {
        background: #D4AF37;
        color: white;
    }

    .btn-primary:hover {
        background: #B8941F;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(212, 175, 55, 0.3);
    }

    .btn-danger {
        background: #DC3545;
        color: white;
    }

    .btn-danger:hover {
        background: #C82333;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }

    .btn-success {
        background: #28A745;
        color: white;
    }

    .btn-success:hover {
        background: #218838;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }

    /* Table Container Styling */
    .table-container {
        background: var(--white);
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .table-container table {
        width: 100%;
        border-collapse: collapse;
        background: var(--white);
    }

    .table-container thead {
        background: linear-gradient(135deg, #D4AF37 0%, #B8941F 100%);
        color: var(--white);
    }

    .table-container th {
        padding: 14px 16px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
    }

    .table-container th:last-child {
        text-align: center;
    }

    .table-container td {
        padding: 12px 16px;
        border-bottom: 1px solid #e8e8e8;
        font-size: 13px;
        color: #333;
        transition: background-color 0.2s;
    }

    .table-container tbody tr {
        transition: all 0.2s ease;
    }

    .table-container tbody tr:nth-child(even) {
        background-color: #fafafa;
    }

    .table-container tbody tr:nth-child(odd) {
        background-color: var(--white);
    }

    .table-container tbody tr:hover {
        background-color: #fff8e1;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transform: translateY(-1px);
    }

    .table-container tbody tr:last-child td {
        border-bottom: none;
    }

    .table-container .text-right {
        text-align: right;
    }

    .table-footer {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        padding: 18px 20px;
        background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
        border-top: 2px solid #D4AF37;
        gap: 15px;
        font-weight: 600;
    }

    .total-label {
        color: #555;
        font-size: 14px;
    }

    .total-amount {
        color: #D4AF37;
        font-size: 20px;
        font-weight: 700;
    }

    .btn-delete {
        background: #DC3545;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
    }

    .btn-delete:hover {
        background: #C82333;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.4);
    }

    .btn-delete:active {
        transform: translateY(0);
        box-shadow: 0 1px 2px rgba(220, 53, 69, 0.3);
    }

    .table-container td:last-child {
        text-align: center;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .filter-container {
            padding: 15px;
        }

        .filter-row {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }

        .table-container {
            overflow-x: auto;
        }

        .table-container th,
        .table-container td {
            padding: 10px 12px;
            font-size: 12px;
        }

        .btn-delete {
            min-width: 32px;
            height: 32px;
            padding: 6px 10px;
            font-size: 12px;
        }
    }
</style>
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
@if(auth()->check() && in_array(auth()->user()->role, ['admin', 'superadmin']))
<script>
    function deleteBarangDalamProses(transaksiId) {
        // Validate input
        if (!transaksiId || transaksiId === 0) {
            alert('ID transaksi tidak valid.');
            return;
        }

        if (!confirm('Apakah Anda yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan dan akan mengurangi stok barang yang terkait.')) {
            return;
        }

        // Create form for DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("report.destroy-barang-dalam-proses") }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        // Add method spoofing for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        // Add transaksi_id
        const transaksiIdInput = document.createElement('input');
        transaksiIdInput.type = 'hidden';
        transaksiIdInput.name = 'transaksi_id';
        transaksiIdInput.value = transaksiId;
        form.appendChild(transaksiIdInput);

        // Submit form
        document.body.appendChild(form);
        form.submit();
    }
</script>
@endif
@endsection

