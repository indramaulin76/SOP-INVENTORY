<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pembelian Bahan Baku - SAE Bakery</title>
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
        <h1>LAPORAN PEMBELIAN BAHAN BAKU</h1>
        <p><strong>SAE BAKERY</strong></p>
        <p>Periode: {{ $start_date ?? 'Semua' }} - {{ $end_date ?? 'Semua' }}</p>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nomor Faktur</th>
                <th>Supplier</th>
                <th>Kode Supplier</th>
                <th>Barang</th>
                <th class="text-right">Qty</th>
                <th>Satuan</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
            @endphp
            @forelse($data as $pembelian)
                @forelse($pembelian->details as $detail)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ \Carbon\Carbon::parse($pembelian->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $pembelian->nomor_faktur }}</td>
                        <td>{{ $pembelian->supplier->nama_supplier ?? '-' }}</td>
                        <td>{{ $pembelian->supplier->kode_supplier ?? '-' }}</td>
                        <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                        <td class="text-right">{{ number_format($detail->quantity, 2) }}</td>
                        <td>{{ $detail->satuan }}</td>
                        <td class="text-right">{{ number_format($detail->harga_beli ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($detail->jumlah ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @empty
                @endforelse
            @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="9" class="text-right"><strong>TOTAL:</strong></td>
                <td class="text-right"><strong>{{ number_format($total, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>






