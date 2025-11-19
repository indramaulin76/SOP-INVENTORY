<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Stock - {{ $barang->nama_barang }} - SAE Bakery</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
        }
        .item-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .item-info h2 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #333;
        }
        .item-details {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 10px;
        }
        .stock-badge {
            background: #ffc107;
            color: #856404;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: 600;
            display: inline-block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #D4AF37;
            font-weight: bold;
            text-align: center;
        }
        .items-in th {
            background-color: #d4edda;
        }
        .items-out th {
            background-color: #f8d7da;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .summary h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .total {
            font-weight: bold;
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>KARTU STOCK</h1>
        <p><strong>SAE BAKERY</strong></p>
        <p>Periode: {{ $start_date ?? 'Semua' }} - {{ $end_date ?? 'Semua' }}</p>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <div class="item-info">
        <h2>{{ $barang->nama_barang }}</h2>
        <div class="item-details">
            <div>
                <strong>Item Code:</strong> {{ $barang->kode_barang }}
            </div>
            <div>
                <strong>Unit:</strong> {{ strtoupper($barang->satuan) }}
            </div>
            <div>
                <strong>Initial Balance:</strong> {{ number_format($initialBalance ?? 0, 2, ',', '.') }} {{ strtoupper($barang->satuan) }}
            </div>
        </div>
        <div style="margin-top: 10px;">
            <span class="stock-badge">Stock: {{ number_format($barang->stok, 2, ',', '.') }} {{ strtoupper($barang->satuan) }}</span>
        </div>
    </div>

    <h3 style="margin: 20px 0 10px 0;">Transaction Summary</h3>

    <!-- Items In Table -->
    <div style="margin-bottom: 30px;">
        <h4 style="background: #d4edda; color: #155724; padding: 10px; margin: 0; border-radius: 5px 5px 0 0;">
            📥 ITEMS IN - Total: {{ number_format($transactionsMasuk->sum('quantity'), 2, ',', '.') }} {{ strtoupper($barang->satuan) }}
        </h4>
        <table class="items-in">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Invoice</th>
                    <th>Supplier</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactionsMasuk as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item['tanggal'] instanceof \Carbon\Carbon ? $item['tanggal']->format('d/m/Y') : $item['tanggal'] }}</td>
                        <td>{{ $item['nomor'] ?? '-' }}</td>
                        <td>{{ $item['supplier'] ?? '-' }}</td>
                        <td class="text-right">{{ number_format($item['quantity'], 2, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada transaksi masuk</td>
                    </tr>
                @endforelse
            </tbody>
            @if($transactionsMasuk->count() > 0)
            <tfoot>
                <tr class="total">
                    <td colspan="6" class="text-right"><strong>Total:</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($totalMasukValue, 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <!-- Items Out Table -->
    <div style="margin-bottom: 30px;">
        <h4 style="background: #f8d7da; color: #721c24; padding: 10px; margin: 0; border-radius: 5px 5px 0 0;">
            📤 ITEMS OUT - Total: {{ number_format($transactionsKeluar->sum('quantity'), 2, ',', '.') }} {{ strtoupper($barang->satuan) }}
        </h4>
        <table class="items-out">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Receipt</th>
                    <th>Customer</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactionsKeluar as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item['tanggal'] instanceof \Carbon\Carbon ? $item['tanggal']->format('d/m/Y') : $item['tanggal'] }}</td>
                        <td>{{ $item['nomor'] ?? '-' }}</td>
                        <td>{{ $item['customer'] ?? '-' }}</td>
                        <td class="text-right">{{ number_format($item['quantity'], 2, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada transaksi keluar</td>
                    </tr>
                @endforelse
            </tbody>
            @if($transactionsKeluar->count() > 0)
            <tfoot>
                <tr class="total">
                    <td colspan="6" class="text-right"><strong>Total:</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($totalKeluarValue, 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <!-- Financial Summary -->
    <div class="summary">
        <h3>Financial Summary</h3>
        <div class="summary-row">
            <strong>Initial Stock Value:</strong>
            <span>Rp {{ number_format($initialStockValue, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <strong>Total Items In Value:</strong>
            <span style="color: #155724;">Rp {{ number_format($totalMasukValue, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <strong>Total Items Out Value:</strong>
            <span style="color: #721c24;">Rp {{ number_format($totalKeluarValue, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row" style="border-top: 2px solid #856404; padding-top: 10px; margin-top: 10px;">
            <strong style="font-size: 14px;">Net Profit:</strong>
            <span style="font-size: 14px; font-weight: bold; color: {{ $netProfit >= 0 ? '#155724' : '#721c24' }};">
                Rp {{ number_format($netProfit, 0, ',', '.') }}
            </span>
        </div>
    </div>

    <div style="margin-top: 20px; text-align: right; font-size: 9px; color: #666;">
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>

