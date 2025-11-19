@extends('report.template-laporan', ['title' => 'Kartu Stock'])

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Kartu Stock</h2>
        </div>
        <div class="card-body">
            <div class="filter-container">
                <div class="filter-title">
                    <span>📋</span>
                    <span>Filter Report</span>
                </div>
                <form method="GET" action="" id="filterForm">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label>Pilih Barang</label>
                            <select name="barang_id" id="barangSelect" onchange="handleFilterChange()">
                                <option value="">-- Pilih Barang --</option>
                                @forelse($barangs ?? [] as $b)
                                    <option value="{{ $b->id }}" {{ request('barang_id') == $b->id ? 'selected' : '' }}>
                                        {{ $b->kode_barang }} - {{ $b->nama_barang }}
                                    </option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" onchange="handleFilterChange()" placeholder="mm/dd/yyyy">
                        </div>
                        <div class="filter-group">
                            <label>Tanggal Akhir</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" onchange="handleFilterChange()" placeholder="mm/dd/yyyy">
                        </div>
                    </div>

                    <div class="filter-row">
                        <div class="filter-group">
                            <label>Bulan</label>
                            <select name="month" onchange="handleFilterChange()">
                                <option value="">-- Semua Bulan --</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(null, $i, 1)->locale('id')->monthName }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Tahun</label>
                            <select name="year" onchange="handleFilterChange()">
                                <option value="">-- Semua Tahun --</option>
                                @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                    <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <span>🔍</span>
                            <span>Cari</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($barang)
    <div class="card mt-20">
        <div class="card-body">
            <!-- Item Information Section -->
            <div style="margin-bottom: 20px; padding: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 8px; border-left: 4px solid var(--primary); position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                    <div>
                        <h3 style="margin: 0 0 10px 0; color: var(--primary); font-size: 24px;">{{ $barang->nama_barang }}</h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                            <div>
                                <strong style="color: #666;">Item Code:</strong> 
                                <span style="color: #333; font-weight: 600;">{{ $barang->kode_barang }}</span>
                            </div>
                            <div>
                                <strong style="color: #666;">Unit:</strong> 
                                <span style="color: #333; font-weight: 600;">{{ strtoupper($barang->satuan) }}</span>
                            </div>
                            <div>
                                <strong style="color: #666;">Initial Balance:</strong> 
                                <span style="color: #333; font-weight: 600;">{{ number_format($initialBalance ?? 0, 2, ',', '.') }} {{ strtoupper($barang->satuan) }}</span>
                            </div>
                        </div>
                    </div>
                    <div style="background: #ffc107; color: #856404; padding: 10px 20px; border-radius: 5px; font-weight: 600; font-size: 18px; white-space: nowrap;">
                        Stock: {{ number_format($barang->stok, 2, ',', '.') }} {{ strtoupper($barang->satuan) }}
                    </div>
                </div>
                <div style="margin-top: 15px; display: flex; gap: 10px;">
                    <button type="button" onclick="window.print()" class="btn btn-primary" style="padding: 8px 20px;">
                        🖨️ Print
                    </button>
                    <button type="button" class="btn btn-danger" onclick="exportPDF(); return false;" style="padding: 8px 20px;">
                        📄 Export PDF
                    </button>
                    <button type="button" class="btn btn-success" onclick="exportExcel(); return false;" style="padding: 8px 20px;">
                        📊 Export Excel
                    </button>
                </div>
            </div>

            <!-- Transaction Summary Header -->
            <div style="margin-bottom: 15px;">
                <h3 style="margin: 0; color: #333;">Transaction Summary</h3>
            </div>

            <!-- Two Column Layout -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                <!-- Kolom Kiri - Items In -->
                <div>
                    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px 8px 0 0; font-weight: 600; text-align: center; font-size: 16px; position: relative;">
                        📥 ITEMS IN
                        @if($transactionsMasuk && $transactionsMasuk->count() > 0)
                        <span style="position: absolute; right: 15px; background: #28a745; color: white; padding: 4px 12px; border-radius: 15px; font-size: 14px;">
                            Total: {{ number_format($transactionsMasuk->sum('quantity'), 2, ',', '.') }} {{ strtoupper($barang->satuan) }}
                        </span>
                        @endif
                    </div>
                    <div class="table-container" style="border: 2px solid #d4edda; border-top: none; max-height: 500px; overflow-y: auto;">
                        <table style="width: 100%;">
                            <thead style="background: #f8f9fa; position: sticky; top: 0;">
                                <tr>
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #d4edda;">DATE</th>
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #d4edda;">INVOICE</th>
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #d4edda;">SUPPLIER</th>
                                    <th style="padding: 12px; text-align: right; border-bottom: 2px solid #d4edda;">QTY</th>
                                    <th style="padding: 12px; text-align: right; border-bottom: 2px solid #d4edda;">UNIT PRICE</th>
                                    <th style="padding: 12px; text-align: right; border-bottom: 2px solid #d4edda;">AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactionsMasuk ?? [] as $transaction)
                                    <tr style="border-bottom: 1px solid #e9ecef;">
                                        <td style="padding: 10px;">{{ \Carbon\Carbon::parse($transaction['tanggal'])->format('d/m/Y') }}</td>
                                        <td style="padding: 10px;">{{ $transaction['nomor'] }}</td>
                                        <td style="padding: 10px;">{{ $transaction['supplier'] }}</td>
                                        <td style="padding: 10px; text-align: right; font-weight: 600; color: #155724;">{{ number_format($transaction['quantity'], 2, ',', '.') }}</td>
                                        <td style="padding: 10px; text-align: right;">Rp {{ number_format($transaction['unit_price'], 0, ',', '.') }}</td>
                                        <td style="padding: 10px; text-align: right; font-weight: 600;">Rp {{ number_format($transaction['amount'], 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: #999; padding: 40px;">Tidak ada transaksi masuk</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($transactionsMasuk && $transactionsMasuk->count() > 0)
                            <tfoot style="background: #f8f9fa; font-weight: 600;">
                                <tr>
                                    <td colspan="5" style="padding: 12px; text-align: right; border-top: 2px solid #d4edda;">Total:</td>
                                    <td style="padding: 12px; text-align: right; border-top: 2px solid #d4edda; color: #155724;">
                                        Rp {{ number_format($transactionsMasuk->sum('amount'), 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Kolom Kanan - Items Out -->
                <div>
                    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px 8px 0 0; font-weight: 600; text-align: center; font-size: 16px; position: relative;">
                        📤 ITEMS OUT
                        @if($transactionsKeluar && $transactionsKeluar->count() > 0)
                        <span style="position: absolute; right: 15px; background: #dc3545; color: white; padding: 4px 12px; border-radius: 15px; font-size: 14px;">
                            Total: {{ number_format($transactionsKeluar->sum('quantity'), 2, ',', '.') }} {{ strtoupper($barang->satuan) }}
                        </span>
                        @endif
                    </div>
                    <div class="table-container" style="border: 2px solid #f8d7da; border-top: none; max-height: 500px; overflow-y: auto;">
                        <table style="width: 100%;">
                            <thead style="background: #f8f9fa; position: sticky; top: 0;">
                                <tr>
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #f8d7da;">DATE</th>
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #f8d7da;">RECEIPT</th>
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #f8d7da;">CUSTOMER</th>
                                    <th style="padding: 12px; text-align: right; border-bottom: 2px solid #f8d7da;">QTY</th>
                                    <th style="padding: 12px; text-align: right; border-bottom: 2px solid #f8d7da;">UNIT PRICE</th>
                                    <th style="padding: 12px; text-align: right; border-bottom: 2px solid #f8d7da;">AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactionsKeluar ?? [] as $transaction)
                                    <tr style="border-bottom: 1px solid #e9ecef;">
                                        <td style="padding: 10px;">{{ \Carbon\Carbon::parse($transaction['tanggal'])->format('d/m/Y') }}</td>
                                        <td style="padding: 10px;">{{ $transaction['nomor'] }}</td>
                                        <td style="padding: 10px;">{{ $transaction['customer'] }}</td>
                                        <td style="padding: 10px; text-align: right; font-weight: 600; color: #721c24;">{{ number_format($transaction['quantity'], 2, ',', '.') }}</td>
                                        <td style="padding: 10px; text-align: right;">Rp {{ number_format($transaction['unit_price'], 0, ',', '.') }}</td>
                                        <td style="padding: 10px; text-align: right; font-weight: 600;">Rp {{ number_format($transaction['amount'], 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: #999; padding: 40px;">Tidak ada transaksi keluar</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($transactionsKeluar && $transactionsKeluar->count() > 0)
                            <tfoot style="background: #f8f9fa; font-weight: 600;">
                                <tr>
                                    <td colspan="5" style="padding: 12px; text-align: right; border-top: 2px solid #f8d7da;">Total:</td>
                                    <td style="padding: 12px; text-align: right; border-top: 2px solid #f8d7da; color: #721c24;">
                                        Rp {{ number_format($transactionsKeluar->sum('amount'), 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            @if(($transactionsMasuk && $transactionsMasuk->count() > 0) || ($transactionsKeluar && $transactionsKeluar->count() > 0))
            <div style="margin-top: 30px; padding: 20px; background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border-radius: 8px; border-left: 4px solid #ffc107;">
                <h4 style="margin: 0 0 15px 0; color: #856404;">Financial Summary</h4>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                    <div>
                        <div style="margin-bottom: 10px;">
                            <strong style="color: #666;">Initial Stock Value:</strong> 
                            <span style="font-size: 16px; color: #333; font-weight: 600;">
                                Rp {{ number_format($initialStockValue ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong style="color: #666;">Total Items In Value:</strong> 
                            <span style="font-size: 16px; color: #155724; font-weight: 600;">
                                Rp {{ number_format($totalMasukValue ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <div style="margin-bottom: 10px;">
                            <strong style="color: #666;">Total Items Out Value:</strong> 
                            <span style="font-size: 16px; color: #721c24; font-weight: 600;">
                                Rp {{ number_format($totalKeluarValue ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong style="color: #666;">Net Profit:</strong> 
                            <span id="netProfit" style="font-size: 18px; font-weight: bold;" class="{{ ($netProfit ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($netProfit ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="card mt-20">
        <div class="card-body">
            <div style="text-align: center; padding: 40px; color: #999;">
                <p>Silakan pilih barang untuk melihat kartu stock</p>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('scripts')
<script>
    function handleFilterChange() {
        // Auto submit jika barang sudah dipilih
        const barangId = document.getElementById('barangSelect').value;
        if (barangId) {
            document.getElementById('filterForm').submit();
        }
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

    // Calculate and display totals on page load
    document.addEventListener('DOMContentLoaded', function() {
        // All calculations are done server-side
        console.log('Kartu Stock loaded');
    });
</script>
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

        .text-success {
            color: #28a745 !important;
        }
        .text-danger {
            color: #dc3545 !important;
        }
        @media (max-width: 768px) {
            .card-body > div[style*="grid-template-columns"] {
                grid-template-columns: 1fr !important;
            }
        }

        @media print {
            .filter-container,
            .btn,
            button {
                display: none !important;
            }
            
            .card {
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>
@endsection
