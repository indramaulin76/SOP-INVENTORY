@extends('report.template-laporan', ['title' => 'Laporan Stock Opname'])

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Laporan Stock Opname</h2>
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
                        <label>Kategori Produk</label>
                        <select name="kategori">
                            <option value="">Semua Kategori</option>
                            @foreach($stockData->pluck('kategori')->unique() as $kategori)
                                <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>{{ $kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Urutkan Berdasarkan</label>
                        <select name="sort_by">
                            <option value="nama" {{ request('sort_by') == 'nama' ? 'selected' : '' }}>Nama</option>
                            <option value="stok_sistem" {{ request('sort_by') == 'stok_sistem' ? 'selected' : '' }}>Stok Sistem</option>
                            <option value="selisih" {{ request('sort_by') == 'selisih' ? 'selected' : '' }}>Selisih</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Urutan</label>
                        <select name="sort_order">
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Naik</option>
                            <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Turun</option>
                        </select>
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <span>🔍</span>
                        <span>Terapkan Filter</span>
                    </button>
                    <a href="{{ route('report.laporan-stock-opname') }}" class="btn btn-secondary">
                        <span>🔄</span>
                        <span>Reset</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@if($stockData && $stockData->count() > 0)
<!-- Charts Section -->
<div class="card mt-20">
    <div class="card-header">
        <h2>📊 Grafik Stock Opname</h2>
    </div>
    <div class="card-body">
        <div class="charts-container">
            <div class="chart-wrapper">
                <h3>Stok Sistem vs Stok Fisik</h3>
                <script type="application/json" id="stockComparisonChartData">{!! json_encode($stockData->take(10)->values()) !!}</script>
                <canvas id="stockComparisonChart" height="80"></canvas>
            </div>
            <div class="chart-wrapper">
                <h3>Grafik Selisih</h3>
                <script type="application/json" id="selisihChartData">{!! json_encode($stockData->take(10)->values()) !!}</script>
                <canvas id="selisihChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>
@endif

<div class="card mt-20">
    <div class="card-body">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Stok Sistem</th>
                        <th>Stok Fisik</th>
                        <th>Selisih</th>
                        <th>Satuan</th>
                        @if(auth()->check() && auth()->user()->role === 'superadmin')
                            <th style="width: 50px;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockData ?? [] as $item)
                        <tr>
                            <td>{{ $item['kode'] }}</td>
                            <td>{{ $item['nama'] }}</td>
                            <td>{{ $item['kategori'] }}</td>
                            <td class="text-right">{{ number_format($item['stok_sistem'], 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item['stok_fisik'], 2, ',', '.') }}</td>
                            <td>
                                @php
                                    $selisihClass = 'selisih-neutral';
                                    if ($item['selisih'] < 0) {
                                        $selisihClass = 'selisih-negative';
                                    } elseif ($item['selisih'] > 0) {
                                        $selisihClass = 'selisih-positive';
                                    }
                                @endphp
                                <span class="{{ $selisihClass }}">
                                    {{ $item['selisih'] >= 0 ? '+' : '' }}{{ number_format($item['selisih'], 2, ',', '.') }}
                                </span>
                            </td>
                            <td>{{ strtoupper($item['satuan']) }}</td>
                            @if(auth()->check() && auth()->user()->role === 'superadmin' && isset($item['barang_id']))
                                <td>
                                    <button 
                                        type="button" 
                                        class="btn-delete" 
                                        onclick="deleteBarang({{ $item['barang_id'] ?? 0 }})"
                                        title="Hapus Barang">
                                        🗑️
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->check() && auth()->user()->role === 'superadmin' ? '8' : '7' }}" style="text-align: center; color: #999; padding: 40px;">Tidak ada data tersedia</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
            text-decoration: none;
        }

        .btn-primary {
            background: #D4AF37;
            color: white;
        }

        .btn-primary:hover {
            background: #B8941F;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .charts-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .chart-wrapper {
            background: var(--white);
            padding: 20px;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
        }

        .chart-wrapper h3 {
            margin: 0 0 15px 0;
            color: var(--gold);
            font-size: 16px;
        }

        @media (max-width: 768px) {
            .charts-container {
                grid-template-columns: 1fr;
            }
        }

        .selisih-positive {
            color: #28a745;
            font-weight: bold;
        }

        .selisih-negative {
            color: #dc3545;
            font-weight: bold;
        }

        .selisih-neutral {
            color: #666;
            font-weight: bold;
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
        function deleteBarang(barangId) {
            // Validate input
            if (!barangId || barangId === 0) {
                alert('ID barang tidak valid.');
                return;
            }

            if (!confirm('Apakah Anda yakin ingin menghapus barang ini? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait.')) {
                return;
            }

            // Create form for DELETE request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("report.destroy-stock-opname-barang") }}';
            
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

            // Add barang_id
            const barangIdInput = document.createElement('input');
            barangIdInput.type = 'hidden';
            barangIdInput.name = 'barang_id';
            barangIdInput.value = barangId;
            form.appendChild(barangIdInput);

            // Submit form
            document.body.appendChild(form);
            form.submit();
        }
    </script>
    @endif

    @if($stockData && $stockData->count() > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx1 = document.getElementById('stockComparisonChart');
                const ctx2 = document.getElementById('selisihChart');
                const dataElement1 = document.getElementById('stockComparisonChartData');
                const dataElement2 = document.getElementById('selisihChartData');
                
                if (!ctx1 || !ctx2 || !dataElement1 || !dataElement2) return;
                
                const stockData = JSON.parse(dataElement1.textContent);
                
                // Prepare data
                const labels = stockData.map(item => item.nama.length > 15 ? item.nama.substring(0, 15) + '...' : item.nama);
                const stokSistem = stockData.map(item => parseFloat(item.stok_sistem));
                const stokFisik = stockData.map(item => parseFloat(item.stok_fisik));
                const selisih = stockData.map(item => parseFloat(item.selisih));

                // Chart 1: Stok Sistem vs Stok Fisik (Bar Chart)
                if (ctx1) {
                    new Chart(ctx1, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Stok Sistem',
                                    data: stokSistem,
                                    backgroundColor: 'rgba(212, 175, 55, 0.7)',
                                    borderColor: '#D4AF37',
                                    borderWidth: 2
                                },
                                {
                                    label: 'Stok Fisik',
                                    data: stokFisik,
                                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                                    borderColor: '#28a745',
                                    borderWidth: 2
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }

                // Chart 2: Selisih (Line Chart)
                if (ctx2) {
                    // Prepare colors for selisih
                    const borderColors = selisih.map(function(val) {
                        if (val < 0) return '#dc3545';
                        if (val > 0) return '#28a745';
                        return '#666';
                    });
                    const backgroundColors = selisih.map(function(val) {
                        if (val < 0) return 'rgba(220, 53, 69, 0.1)';
                        if (val > 0) return 'rgba(40, 167, 69, 0.1)';
                        return 'rgba(102, 102, 102, 0.1)';
                    });

                    new Chart(ctx2, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Selisih (Fisik - Sistem)',
                                data: selisih,
                                borderColor: borderColors,
                                backgroundColor: backgroundColors,
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 5,
                                pointHoverRadius: 7
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: false
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endif
@endsection
