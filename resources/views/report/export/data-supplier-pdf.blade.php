<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Supplier - SAE Bakery</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA SUPPLIER</h1>
        <p><strong>SAE BAKERY</strong></p>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Supplier</th>
                <th>Nama Supplier</th>
                <th>Alamat</th>
                <th>Telepon</th>
                <th>Email</th>
                <th>Nama Pemilik</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->kode_supplier }}</td>
                    <td>{{ $item->nama_supplier }}</td>
                    <td>{{ $item->alamat ?? '-' }}</td>
                    <td>{{ $item->telepon ?? '-' }}</td>
                    <td>{{ $item->email ?? '-' }}</td>
                    <td>{{ $item->nama_pemilik ?? '-' }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>






