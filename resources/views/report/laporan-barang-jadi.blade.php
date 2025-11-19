@extends('report.template-laporan', ['title' => 'Laporan Barang Jadi'])

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Laporan Barang Jadi</h2>
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
                            <label>Nomor Invoice</label>
                            <input type="text" name="invoice_number" value="{{ request('invoice_number') }}" placeholder="Cari invoice...">
                        </div>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">🔍 Cari</button>
                        <button type="button" class="btn btn-secondary">📄 Export PDF</button>
                        <button type="button" class="btn btn-success">📊 Export Excel</button>
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data ?? [] as $row)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                                <td>{{ $row->nomor_faktur }}</td>
                                <td>
                                    @forelse($row->details as $detail)
                                        <div>{{ $detail->barang_nama }}</div>
                                    @empty
                                        -
                                    @endforelse
                                </td>
                                <td>
                                    @forelse($row->details as $detail)
                                        <div>{{ $detail->barang_qty }}</div>
                                    @empty
                                        -
                                    @endforelse
                                </td>
                                <td>
                                    @forelse($row->details as $detail)
                                        <div>{{ $detail->barang_satuan }}</div>
                                    @empty
                                        -
                                    @endforelse
                                </td>
                                <td>
                                    @forelse($row->details as $detail)
                                        <div>Rp{{ number_format($detail->barang_harga, 0, ',', '.') }}</div>
                                    @empty
                                        -
                                    @endforelse
                                </td>
                                <td class="text-right">Rp{{ number_format($row->total_harga, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: #999; padding: 40px;">Tidak ada data tersedia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="table-footer">
                    <span class="total-label">Total Barang Jadi:</span>
                    <span class="total-amount">Rp{{ number_format($total ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
