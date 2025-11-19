@extends('report.template-laporan', ['title' => 'Laporan Stock Akhir'])

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Laporan Stock Akhir</h2>
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
                            <label>Nomor Invoice</label>
                            <input type="text" name="invoice_number" value="{{ request('invoice_number') }}" placeholder="Cari invoice...">
                        </div>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <span>🔍</span>
                            <span>Cari</span>
                        </button>
                        <button type="button" class="btn btn-danger" onclick="exportPDF()">
                            <span>📄</span>
                            <span>Export PDF</span>
                        </button>
                        <button type="button" class="btn btn-success" onclick="exportExcel()">
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
                            @if(auth()->check() && auth()->user()->role === 'superadmin')
                                <th style="width: 50px;">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data ?? [] as $transaction)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($transaction['tanggal'])->format('d/m/Y') }}</td>
                                <td>{{ $transaction['nomor_invoice'] }}</td>
                                <td>{{ $transaction['deskripsi'] }}</td>
                                <td>{{ $transaction['supplier'] }}</td>
                                <td>{{ $transaction['kode_supplier'] }}</td>
                                <td>{{ $transaction['item'] }}</td>
                                <td>{{ $transaction['kode_item'] }}</td>
                                <td class="text-right">{{ number_format($transaction['qty'], 2, ',', '.') }}</td>
                                <td>{{ strtoupper($transaction['unit']) }}</td>
                                <td class="text-right">Rp {{ number_format($transaction['harga'], 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($transaction['jumlah'], 0, ',', '.') }}</td>
                                @if(auth()->check() && auth()->user()->role === 'superadmin' && isset($transaction['transaksi_id']) && isset($transaction['transaksi_type']))
                                    <td>
                                        <button 
                                            type="button" 
                                            class="btn-delete" 
                                            onclick="deleteTransaction({{ $transaction['transaksi_id'] ?? 0 }}, '{{ $transaction['transaksi_type'] ?? '' }}')"
                                            title="Hapus Transaksi">
                                            🗑️
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->check() && auth()->user()->role === 'superadmin' ? '12' : '11' }}" style="text-align: center; color: #999; padding: 40px;">Tidak ada data tersedia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="table-footer">
                    <span class="total-label">Total:</span>
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
    }

    .btn-danger {
        background: #DC3545;
        color: white;
    }

    .btn-danger:hover {
        background: #C82333;
    }

    .btn-success {
        background: #28A745;
        color: white;
    }

    .btn-success:hover {
        background: #218838;
    }

    /* Table Styling */
    .table-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .table-container table {
        width: 100%;
        border-collapse: collapse;
    }

    .table-container thead {
        background: linear-gradient(135deg, #D4AF37 0%, #B8941F 100%);
        color: white;
    }

    .table-container th {
        padding: 12px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
        white-space: nowrap;
    }

    .table-container td {
        padding: 12px;
        border-bottom: 1px solid #e0e0e0;
        font-size: 13px;
        color: #333;
    }

    .table-container tbody tr:hover {
        background-color: #f9f9f9;
    }

    .table-container tbody tr:last-child td {
        border-bottom: none;
    }

    .text-right {
        text-align: right;
    }

    .table-footer {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        padding: 15px 20px;
        background-color: #f5f5f5;
        border-top: 2px solid #e0e0e0;
        gap: 15px;
        font-weight: 600;
    }

    .total-label {
        color: #333;
    }

    .total-amount {
        color: #D4AF37;
        font-size: 18px;
    }

    .btn-delete {
        background: #DC3545;
        color: white;
        border: none;
        padding: 6px 10px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-delete:hover {
        background: #C82333;
    }
</style>
@endsection

@section('scripts')
@if(auth()->check() && auth()->user()->role === 'superadmin')
<script>
    function deleteTransaction(transaksiId, transaksiType) {
        // Validate input
        if (!transaksiId || !transaksiType) {
            alert('Data transaksi tidak valid.');
            return;
        }

        if (!confirm('Apakah Anda yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan.')) {
            return;
        }

        // Create form for DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("report.destroy-stock-akhir") }}';
        
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

        // Add transaksi_type
        const transaksiTypeInput = document.createElement('input');
        transaksiTypeInput.type = 'hidden';
        transaksiTypeInput.name = 'transaksi_type';
        transaksiTypeInput.value = transaksiType;
        form.appendChild(transaksiTypeInput);

        // Submit form
        document.body.appendChild(form);
        form.submit();
    }
</script>
@endif

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
