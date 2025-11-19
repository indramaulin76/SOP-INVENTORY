<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pemakaian Bahan Baku - SAE Bakery</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
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
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
        }
        .total {
            font-weight: bold;
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PEMAKAIAN BAHAN BAKU</h1>
        <p><strong>SAE BAKERY</strong></p>
        <p>Periode: {{ $start_date ?? 'Semua' }} - {{ $end_date ?? 'Semua' }}</p>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nomor Bukti</th>
                <th>Keterangan</th>
                <th>Customer</th>
                <th>Kode Customer</th>
                <th>Item</th>
                <th>Kode Item</th>
                <th class="text-right">Qty</th>
                <th>Unit</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        @if(is_string($item['tanggal']))
                            {{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y') }}
                        @elseif(is_object($item['tanggal']) && method_exists($item['tanggal'], 'format'))
                            {{ $item['tanggal']->format('d/m/Y') }}
                        @else
                            {{ $item['tanggal'] ?? '-' }}
                        @endif
                    </td>
                    <td>{{ $item['nomor_bukti'] ?? '-' }}</td>
                    <td>{{ $item['keterangan'] ?? '-' }}</td>
                    <td>{{ $item['customer'] ?? '-' }}</td>
                    <td>{{ $item['kode_customer'] ?? '-' }}</td>
                    <td>{{ $item['item'] ?? '-' }}</td>
                    <td>{{ $item['kode_item'] ?? '-' }}</td>
                    <td class="text-right">{{ number_format($item['qty'] ?? 0, 2) }}</td>
                    <td>{{ $item['unit'] ?? '-' }}</td>
                    <td class="text-right">{{ number_format($item['jumlah'] ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="10" class="text-right"><strong>TOTAL:</strong></td>
                <td class="text-right"><strong>{{ number_format($total, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>






