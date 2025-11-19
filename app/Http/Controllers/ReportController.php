<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangDalamProses;
use App\Models\BarangJadi;
use App\Models\Customer;
use App\Models\PembelianBahanBaku;
use App\Models\PemakaianBahanBaku;
use App\Models\PemakaianBarangDalamProses;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportController extends Controller
{
    /**
     * Laporan Pembelian Bahan Baku
     */
    public function laporanPembelianBahanBaku(Request $request)
    {
        $query = PembelianBahanBaku::with(['supplier', 'details.barang']);

        // Filter berdasarkan tanggal
        if ($request->start_date) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        // Filter berdasarkan supplier
        if ($request->supplier) {
            $query->where('supplier_id', $request->supplier);
        }

        // Filter berdasarkan invoice number
        if ($request->invoice_number) {
            $query->where('nomor_faktur', 'like', '%' . $request->invoice_number . '%');
        }

        $data = $query->orderBy('tanggal', 'desc')->get();
        $total = $data->sum('total_harga');
        $suppliers = Supplier::all();

        // Handle export
        if ($request->export == 'pdf') {
            return $this->exportPembelianBahanBakuPDF($request, $data, $total);
        }
        if ($request->export == 'excel') {
            return $this->exportPembelianBahanBakuExcel($request, $data, $total);
        }

        return view('report.laporan-pembelian-bahan-baku', compact('data', 'total', 'suppliers'));
    }

    /**
     * Laporan Barang Dalam Proses
     */
    public function laporanBarangDalamProses(Request $request)
    {
        $query = BarangDalamProses::with('details');

        if ($request->start_date) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        if ($request->invoice_number) {
            $query->where('nomor_faktur', 'like', '%' . $request->invoice_number . '%');
        }

        $barangDalamProses = $query->orderBy('tanggal', 'desc')->get();
        
        // Transform data: setiap detail menjadi baris terpisah
        // Filter hanya transaksi yang memiliki detail valid
        $data = collect([]);
        $processedFakturIds = []; // Track faktur yang sudah diproses berdasarkan ID
        
        foreach ($barangDalamProses as $proses) {
            // Skip jika transaksi ini sudah diproses (berdasarkan ID untuk menghindari duplikasi)
            if (in_array($proses->id, $processedFakturIds)) {
                continue;
            }
            
            // Hanya proses jika memiliki detail
            if ($proses->details && $proses->details->count() > 0) {
                $hasValidDetail = false;
                
                foreach ($proses->details as $detail) {
                    if ($detail) {
                        $barangNama = trim($detail->barang_nama ?? '');
                        $qty = (float)($detail->barang_qty ?? 0);
                        $total = (float)($detail->barang_jumlah ?? 0);
                        
                        // Skip jika data tidak valid: barang kosong, qty <= 0, atau total <= 0
                        if (empty($barangNama) || $qty <= 0 || $total <= 0) {
                            continue;
                        }
                        
                        $hasValidDetail = true;
                        $data->push([
                            'transaksi_id' => $proses->id,
                            'tanggal' => $proses->tanggal,
                            'nomor_faktur' => $proses->nomor_faktur,
                            'barang' => $barangNama,
                            'qty' => $qty,
                            'satuan' => $detail->barang_satuan ?? '-',
                            'harga' => (float)($detail->barang_harga ?? 0),
                            'total' => $total,
                        ]);
                    }
                }
                
                // Tandai faktur ini sudah diproses jika ada detail valid
                if ($hasValidDetail) {
                    $processedFakturIds[] = $proses->id;
                }
            }
        }
        
        $total = $data->sum('total');

        // Handle export
        if ($request->export == 'pdf') {
            return $this->exportBarangDalamProsesPDF($request, $data, $total);
        }
        if ($request->export == 'excel') {
            return $this->exportBarangDalamProsesExcel($request, $data, $total);
        }

        return view('report.laporan-barang-dalam-proses', compact('data', 'total'));
    }

    /**
     * Laporan Barang Jadi
     */
    public function laporanBarangJadi(Request $request)
    {
        $query = BarangJadi::with('details');

        if ($request->start_date) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        if ($request->invoice_number) {
            $query->where('nomor_faktur', 'like', '%' . $request->invoice_number . '%');
        }

        $data = $query->orderBy('tanggal', 'desc')->get();
        $total = $data->sum('total_harga');

        return view('report.laporan-barang-jadi', compact('data', 'total'));
    }

    /**
     * Laporan Penjualan
     */
    public function laporanPenjualan(Request $request)
    {
        $query = \App\Models\PenjualanBarangJadi::with(['details' => function($q) {
            $q->with('barang');
        }]);

        // Filter berdasarkan tanggal
        if ($request->start_date) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        // Filter berdasarkan customer
        if ($request->customer) {
            $query->where(function($q) use ($request) {
                $q->where('nama_customer', 'like', '%' . $request->customer . '%')
                  ->orWhere('kode_customer', 'like', '%' . $request->customer . '%');
            });
        }

        // Filter berdasarkan item
        if ($request->item) {
            $query->whereHas('details', function($q) use ($request) {
                $q->where('barang_id', $request->item);
            });
        }

        // Filter berdasarkan nomor bukti
        if ($request->nomor_bukti) {
            $query->where('nomor_bukti', 'like', '%' . $request->nomor_bukti . '%');
        }

        $penjualans = $query->orderBy('tanggal', 'desc')->get();

        // Transform data untuk ditampilkan di tabel
        $data = collect([]);
        foreach ($penjualans as $penjualan) {
            if ($penjualan->details && $penjualan->details->count() > 0) {
                foreach ($penjualan->details as $detail) {
                    if ($detail) {
                        $data->push([
                            'tanggal' => $penjualan->tanggal,
                            'nomor_bukti' => $penjualan->nomor_bukti ?? '-',
                            'keterangan' => $penjualan->keterangan ?? '-',
                            'customer' => $penjualan->nama_customer ?? '-',
                            'kode_customer' => $penjualan->kode_customer ?? '-',
                            'item' => $detail->barang->nama_barang ?? ($detail->barang_nama ?? '-'),
                            'kode_item' => $detail->barang->kode_barang ?? ($detail->barang_kode ?? '-'),
                            'qty' => $detail->quantity ?? 0,
                            'unit' => $detail->satuan ?? '-',
                            'harga' => $detail->harga ?? 0,
                            'jumlah' => $detail->jumlah ?? 0,
                        ]);
                    }
                }
            }
        }

        $total = $data->sum('jumlah');
        $barangs = Barang::orderBy('nama_barang')->get();

        // Handle export
        if ($request->export == 'pdf') {
            return $this->exportPenjualanPDF($request, $data, $total);
        }
        if ($request->export == 'excel') {
            return $this->exportPenjualanExcel($request, $data, $total);
        }

        return view('report.laporan-penjualan', compact('data', 'total', 'barangs'));
    }

    /**
     * Laporan Pemakaian Bahan Baku
     */
    public function laporanPemakaianBahanBaku(Request $request)
    {
        $query = PemakaianBahanBaku::with(['details' => function($q) {
            $q->with('barang');
        }]);

        // Filter berdasarkan tanggal
        if ($request->start_date) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        // Filter berdasarkan customer
        if ($request->customer) {
            $query->where(function($q) use ($request) {
                $q->where('nama_customer', 'like', '%' . $request->customer . '%')
                  ->orWhere('kode_customer', 'like', '%' . $request->customer . '%');
            });
        }

        // Filter berdasarkan item
        if ($request->item) {
            $query->whereHas('details', function($q) use ($request) {
                $q->where('barang_id', $request->item);
            });
        }

        // Filter berdasarkan nomor bukti
        if ($request->nomor_bukti) {
            $query->where('nomor_bukti', 'like', '%' . $request->nomor_bukti . '%');
        }

        $pemakaianBahans = $query->orderBy('tanggal', 'desc')->get();

        // Transform data untuk ditampilkan di tabel
        $data = collect([]);
        foreach ($pemakaianBahans as $pemakaian) {
            if ($pemakaian->details && $pemakaian->details->count() > 0) {
                foreach ($pemakaian->details as $detail) {
                    if ($detail) {
                        $data->push([
                            'tanggal' => $pemakaian->tanggal,
                            'nomor_bukti' => $pemakaian->nomor_bukti ?? '-',
                            'keterangan' => $pemakaian->keterangan ?? '-',
                            'customer' => $pemakaian->nama_customer ?? '-',
                            'kode_customer' => $pemakaian->kode_customer ?? '-',
                            'item' => $detail->barang->nama_barang ?? ($detail->barang_nama ?? '-'),
                            'kode_item' => $detail->barang->kode_barang ?? ($detail->barang_kode ?? '-'),
                            'qty' => $detail->quantity ?? 0,
                            'unit' => $detail->satuan ?? '-',
                            'harga' => $detail->harga ?? 0,
                            'jumlah' => $detail->jumlah ?? 0,
                        ]);
                    }
                }
            }
        }

        $total = $data->sum('jumlah');
        $barangs = Barang::orderBy('nama_barang')->get();

        // Handle export
        if ($request->export == 'pdf') {
            return $this->exportPemakaianBahanBakuPDF($request, $data, $total);
        }
        if ($request->export == 'excel') {
            return $this->exportPemakaianBahanBakuExcel($request, $data, $total);
        }

        return view('report.laporan-pemakaian-bahan-baku', compact('data', 'total', 'barangs'));
    }

    /**
     * Laporan Pemakaian Barang Dalam Proses
     */
    public function laporanPemakaianBarangDalamProses(Request $request)
    {
        $query = PemakaianBarangDalamProses::with(['details' => function($q) {
            $q->with('barang');
        }]);

        // Filter berdasarkan tanggal
        if ($request->start_date) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        // Filter berdasarkan customer/departemen
        if ($request->customer) {
            $query->where(function($q) use ($request) {
                $q->where('nama_customer', 'like', '%' . $request->customer . '%')
                  ->orWhere('kode_customer', 'like', '%' . $request->customer . '%');
            });
        }

        // Filter berdasarkan nomor bukti
        if ($request->nomor_bukti) {
            $query->where('nomor_bukti', 'like', '%' . $request->nomor_bukti . '%');
        }

        $pemakaianProses = $query->orderBy('tanggal', 'desc')->get();

        // Transform data untuk ditampilkan di tabel (setiap detail menjadi satu baris)
        $data = collect([]);
        foreach ($pemakaianProses as $pemakaian) {
            if ($pemakaian->details && $pemakaian->details->count() > 0) {
                foreach ($pemakaian->details as $detail) {
                    if ($detail) {
                        $data->push([
                            'pemakaian_id' => $pemakaian->id,
                            'tanggal' => $pemakaian->tanggal,
                            'nomor_bukti' => $pemakaian->nomor_bukti ?? '-',
                            'departemen' => $pemakaian->nama_customer ?? '-',
                            'barang' => $detail->barang->nama_barang ?? ($detail->barang_nama ?? '-'),
                            'qty' => $detail->quantity ?? 0,
                            'satuan' => $detail->satuan ?? '-',
                            'harga' => $detail->harga ?? 0,
                            'total' => $detail->jumlah ?? 0,
                        ]);
                    }
                }
            }
        }

        $total = $data->sum('total');

        return view('report.laporan-pemakaian-barang-dalam-proses', compact('data', 'total'));
    }

    /**
     * Laporan Data Barang
     */
    public function laporanDataBarang(Request $request)
    {
        $query = Barang::query();

        // Filter by kategori (deskripsi)
        if ($request->kategori) {
            $query->where('deskripsi', 'like', '%' . $request->kategori . '%');
        }

        // Search by name or code
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama_barang', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_barang', 'like', '%' . $request->search . '%');
            });
        }

        $data = $query->orderBy('nama_barang', 'asc')->get();

        // Handle export
        if ($request->export == 'pdf') {
            return $this->exportDataBarangPDF($request, $data);
        }
        if ($request->export == 'excel') {
            return $this->exportDataBarangExcel($request, $data);
        }

        return view('report.laporan-data-barang', compact('data'));
    }

    /**
     * Laporan Data Customer
     */
    public function laporanDataCustomer(Request $request)
    {
        $query = Customer::query();

        // Filter by tipe
        if ($request->tipe) {
            $query->where('tipe_customer', $request->tipe);
        }

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama_customer', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_customer', 'like', '%' . $request->search . '%');
            });
        }

        $data = $query->orderBy('nama_customer', 'asc')->get();

        // Handle export
        if ($request->export == 'pdf') {
            return $this->exportDataCustomerPDF($request, $data);
        }
        if ($request->export == 'excel') {
            return $this->exportDataCustomerExcel($request, $data);
        }

        return view('report.laporan-data-customer', compact('data'));
    }

    /**
     * Laporan Data Supplier
     */
    public function laporanDataSupplier(Request $request)
    {
        $query = Supplier::query();

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama_supplier', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_supplier', 'like', '%' . $request->search . '%');
            });
        }

        $data = $query->orderBy('nama_supplier', 'asc')->get();

        // Handle export
        if ($request->export == 'pdf') {
            return $this->exportDataSupplierPDF($request, $data);
        }
        if ($request->export == 'excel') {
            return $this->exportDataSupplierExcel($request, $data);
        }

        return view('report.laporan-data-supplier', compact('data'));
    }

    /**
     * Kartu Stock
     */
    public function kartuStock(Request $request)
    {
        $barangId = $request->barang_id;
        $barang = null;
        $transactionsMasuk = collect([]);
        $transactionsKeluar = collect([]);
        $initialBalance = 0;
        $initialStockValue = 0;
        $totalMasukValue = 0;
        $totalKeluarValue = 0;
        $netProfit = 0;

        if ($barangId) {
            $barang = Barang::find($barangId);
            if ($barang) {
                // Get all transactions for this barang
                // Input transactions (Masuk)
                $pembelianQuery = \App\Models\PembelianBahanBakuDetail::where('barang_id', $barangId)
                    ->with('pembelianBahanBaku');

                $prosesQuery = \App\Models\BarangDalamProsesDetail::where('barang_kode', $barang->kode_barang)
                    ->with('barangDalamProses');

                $jadiQuery = \App\Models\BarangJadiDetail::where('barang_kode', $barang->kode_barang)
                    ->with('barangJadi');

                // Output transactions (Keluar)
                $pemakaianBahanQuery = \App\Models\PemakaianBahanBakuDetail::where('barang_id', $barangId)
                    ->with('pemakaianBahanBaku');

                $pemakaianProsesQuery = \App\Models\PemakaianBarangDalamProsesDetail::where('barang_id', $barangId)
                    ->with('pemakaianBarangDalamProses');

                $penjualanJadiQuery = \App\Models\PenjualanBarangJadiDetail::where('barang_id', $barangId)
                    ->with('penjualanBarangJadi');

                // Filter by date
                if ($request->start_date) {
                    $pembelianQuery->whereHas('pembelianBahanBaku', function($q) use ($request) {
                        $q->whereDate('tanggal', '>=', $request->start_date);
                    });
                    $prosesQuery->whereHas('barangDalamProses', function($q) use ($request) {
                        $q->whereDate('tanggal', '>=', $request->start_date);
                    });
                    $jadiQuery->whereHas('barangJadi', function($q) use ($request) {
                        $q->whereDate('tanggal', '>=', $request->start_date);
                    });
                    $pemakaianBahanQuery->whereHas('pemakaianBahanBaku', function($q) use ($request) {
                        $q->whereDate('tanggal', '>=', $request->start_date);
                    });
                    $pemakaianProsesQuery->whereHas('pemakaianBarangDalamProses', function($q) use ($request) {
                        $q->whereDate('tanggal', '>=', $request->start_date);
                    });
                    $penjualanJadiQuery->whereHas('penjualanBarangJadi', function($q) use ($request) {
                        $q->whereDate('tanggal', '>=', $request->start_date);
                    });
                }

                if ($request->end_date) {
                    $pembelianQuery->whereHas('pembelianBahanBaku', function($q) use ($request) {
                        $q->whereDate('tanggal', '<=', $request->end_date);
                    });
                    $prosesQuery->whereHas('barangDalamProses', function($q) use ($request) {
                        $q->whereDate('tanggal', '<=', $request->end_date);
                    });
                    $jadiQuery->whereHas('barangJadi', function($q) use ($request) {
                        $q->whereDate('tanggal', '<=', $request->end_date);
                    });
                    $pemakaianBahanQuery->whereHas('pemakaianBahanBaku', function($q) use ($request) {
                        $q->whereDate('tanggal', '<=', $request->end_date);
                    });
                    $pemakaianProsesQuery->whereHas('pemakaianBarangDalamProses', function($q) use ($request) {
                        $q->whereDate('tanggal', '<=', $request->end_date);
                    });
                    $penjualanJadiQuery->whereHas('penjualanBarangJadi', function($q) use ($request) {
                        $q->whereDate('tanggal', '<=', $request->end_date);
                    });
                }

                // Filter by month
                if ($request->month) {
                    $pembelianQuery->whereHas('pembelianBahanBaku', function($q) use ($request) {
                        $q->whereMonth('tanggal', $request->month);
                    });
                    $prosesQuery->whereHas('barangDalamProses', function($q) use ($request) {
                        $q->whereMonth('tanggal', $request->month);
                    });
                    $jadiQuery->whereHas('barangJadi', function($q) use ($request) {
                        $q->whereMonth('tanggal', $request->month);
                    });
                    $pemakaianBahanQuery->whereHas('pemakaianBahanBaku', function($q) use ($request) {
                        $q->whereMonth('tanggal', $request->month);
                    });
                    $pemakaianProsesQuery->whereHas('pemakaianBarangDalamProses', function($q) use ($request) {
                        $q->whereMonth('tanggal', $request->month);
                    });
                    $penjualanJadiQuery->whereHas('penjualanBarangJadi', function($q) use ($request) {
                        $q->whereMonth('tanggal', $request->month);
                    });
                }

                // Filter by year
                if ($request->year) {
                    $pembelianQuery->whereHas('pembelianBahanBaku', function($q) use ($request) {
                        $q->whereYear('tanggal', $request->year);
                    });
                    $prosesQuery->whereHas('barangDalamProses', function($q) use ($request) {
                        $q->whereYear('tanggal', $request->year);
                    });
                    $jadiQuery->whereHas('barangJadi', function($q) use ($request) {
                        $q->whereYear('tanggal', $request->year);
                    });
                    $pemakaianBahanQuery->whereHas('pemakaianBahanBaku', function($q) use ($request) {
                        $q->whereYear('tanggal', $request->year);
                    });
                    $pemakaianProsesQuery->whereHas('pemakaianBarangDalamProses', function($q) use ($request) {
                        $q->whereYear('tanggal', $request->year);
                    });
                    $penjualanJadiQuery->whereHas('penjualanBarangJadi', function($q) use ($request) {
                        $q->whereYear('tanggal', $request->year);
                    });
                }

                $pembelianDetails = $pembelianQuery->get();
                $prosesDetails = $prosesQuery->get();
                $jadiDetails = $jadiQuery->get();
                $pemakaianBahanDetails = $pemakaianBahanQuery->get();
                $pemakaianProsesDetails = $pemakaianProsesQuery->get();
                $penjualanJadiDetails = $penjualanJadiQuery->get();

                // Separate transactions by type
                // Transaksi Masuk
                $transactionsMasuk = collect()
                    // Masuk - Pembelian Bahan Baku
                    ->merge($pembelianDetails->map(function($item) {
                        return [
                            'tanggal' => $item->pembelianBahanBaku->tanggal,
                            'keterangan' => 'Pembelian Bahan Baku',
                            'nomor' => $item->pembelianBahanBaku->nomor_faktur,
                            'supplier' => $item->pembelianBahanBaku->nama_supplier ?? '-',
                            'quantity' => $item->quantity,
                            'unit_price' => $item->harga_beli ?? 0,
                            'amount' => $item->jumlah ?? 0,
                        ];
                    }))
                    // Masuk - Barang Dalam Proses
                    ->merge($prosesDetails->map(function($item) {
                        return [
                            'tanggal' => $item->barangDalamProses->tanggal,
                            'keterangan' => 'Barang Dalam Proses',
                            'nomor' => $item->barangDalamProses->nomor_faktur,
                            'supplier' => $item->barangDalamProses->nama_supplier ?? '-',
                            'quantity' => $item->barang_qty,
                            'unit_price' => $item->barang_harga ?? 0,
                            'amount' => $item->barang_jumlah ?? 0,
                        ];
                    }))
                    // Masuk - Barang Jadi
                    ->merge($jadiDetails->map(function($item) {
                        return [
                            'tanggal' => $item->barangJadi->tanggal,
                            'keterangan' => 'Barang Jadi',
                            'nomor' => $item->barangJadi->nomor_faktur,
                            'supplier' => $item->barangJadi->nama_supplier ?? '-',
                            'quantity' => $item->barang_qty,
                            'unit_price' => $item->barang_harga ?? 0,
                            'amount' => $item->barang_jumlah ?? 0,
                        ];
                    }))
                    ->sortBy('tanggal')
                    ->values();

                // Transaksi Keluar
                $transactionsKeluar = collect()
                    // Keluar - Pemakaian Bahan Baku
                    ->merge($pemakaianBahanDetails->map(function($item) {
                        return [
                            'tanggal' => $item->pemakaianBahanBaku->tanggal,
                            'keterangan' => 'Pemakaian Bahan Baku',
                            'nomor' => $item->pemakaianBahanBaku->nomor_bukti,
                            'customer' => $item->pemakaianBahanBaku->nama_customer ?? '-',
                            'quantity' => $item->quantity,
                            'unit_price' => $item->harga ?? 0,
                            'amount' => $item->jumlah ?? 0,
                        ];
                    }))
                    // Keluar - Pemakaian Barang Dalam Proses
                    ->merge($pemakaianProsesDetails->map(function($item) {
                        return [
                            'tanggal' => $item->pemakaianBarangDalamProses->tanggal,
                            'keterangan' => 'Pemakaian Barang Dalam Proses',
                            'nomor' => $item->pemakaianBarangDalamProses->nomor_bukti,
                            'customer' => $item->pemakaianBarangDalamProses->nama_customer ?? '-',
                            'quantity' => $item->quantity,
                            'unit_price' => $item->harga ?? 0,
                            'amount' => $item->jumlah ?? 0,
                        ];
                    }))
                    // Keluar - Penjualan Barang Jadi
                    ->merge($penjualanJadiDetails->map(function($item) {
                        return [
                            'tanggal' => $item->penjualanBarangJadi->tanggal,
                            'keterangan' => 'Penjualan Barang Jadi',
                            'nomor' => $item->penjualanBarangJadi->nomor_bukti,
                            'customer' => $item->penjualanBarangJadi->nama_customer ?? '-',
                            'quantity' => $item->quantity,
                            'unit_price' => $item->harga ?? 0,
                            'amount' => $item->jumlah ?? 0,
                        ];
                    }))
                    ->sortBy('tanggal')
                    ->values();

                // Calculate initial balance (stok sebelum periode filter)
                // Initial balance = stok saat ini - (total masuk - total keluar) dalam periode
                $totalMasuk = $transactionsMasuk->sum('quantity');
                $totalKeluar = $transactionsKeluar->sum('quantity');
                $initialBalance = $barang->stok - ($totalMasuk - $totalKeluar);
                
                // Calculate initial stock value (using harga_beli)
                $initialStockValue = $initialBalance * ($barang->harga_beli ?? 0);
                
                // Calculate totals
                $totalMasukValue = $transactionsMasuk->sum('amount');
                $totalKeluarValue = $transactionsKeluar->sum('amount');
                $netProfit = $totalKeluarValue - $totalMasukValue;
            }
        }

        $barangs = Barang::orderBy('nama_barang')->get();

        // Handle export
        if ($request->export == 'pdf') {
            return $this->exportKartuStockPDF($request, $barang, $transactionsMasuk, $transactionsKeluar, $initialBalance, $initialStockValue, $totalMasukValue, $totalKeluarValue, $netProfit);
        }
        if ($request->export == 'excel') {
            return $this->exportKartuStockExcel($request, $barang, $transactionsMasuk, $transactionsKeluar, $initialBalance, $initialStockValue, $totalMasukValue, $totalKeluarValue, $netProfit);
        }

        return view('report.kartu-stock', compact(
            'barang', 
            'transactionsMasuk', 
            'transactionsKeluar', 
            'barangs',
            'initialBalance',
            'initialStockValue',
            'totalMasukValue',
            'totalKeluarValue',
            'netProfit'
        ));
    }

    /**
     * Laporan Stock Akhir
     */
    public function laporanStockAkhir(Request $request)
    {
        // Get all transactions that affect stock (both masuk and keluar)
        $allTransactions = collect([]);

        // Get Pembelian Bahan Baku (Masuk)
        $pembelianQuery = PembelianBahanBaku::with(['details' => function($q) {
            $q->with('barang');
        }, 'supplier']);
        if ($request->start_date) {
            $pembelianQuery->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $pembelianQuery->whereDate('tanggal', '<=', $request->end_date);
        }
        if ($request->supplier) {
            $pembelianQuery->where('supplier_id', $request->supplier);
        }
        if ($request->item) {
            $pembelianQuery->whereHas('details', function($q) use ($request) {
                $q->where('barang_id', $request->item);
            });
        }
        if ($request->invoice_number) {
            $pembelianQuery->where('nomor_faktur', 'like', '%' . $request->invoice_number . '%');
        }
        $pembelians = $pembelianQuery->get();

        foreach ($pembelians as $pembelian) {
            if ($pembelian->details && $pembelian->details->count() > 0) {
            foreach ($pembelian->details as $detail) {
                    if ($detail) {
                $allTransactions->push([
                            'transaksi_id' => $pembelian->id,
                            'transaksi_type' => 'pembelian_bahan_baku',
                    'tanggal' => $pembelian->tanggal,
                            'nomor_invoice' => $pembelian->nomor_faktur ?? '-',
                    'deskripsi' => 'Pembelian Bahan Baku',
                    'supplier' => $pembelian->supplier->nama_supplier ?? '-',
                    'kode_supplier' => $pembelian->supplier->kode_supplier ?? '-',
                            'item' => $detail->barang->nama_barang ?? ($detail->barang_nama ?? '-'),
                            'kode_item' => $detail->barang->kode_barang ?? ($detail->barang_kode ?? '-'),
                            'qty' => $detail->quantity ?? 0,
                            'unit' => $detail->satuan ?? '-',
                    'harga' => $detail->harga_beli ?? 0,
                    'jumlah' => $detail->jumlah ?? 0,
                ]);
                    }
                }
            }
        }

        // Get Barang Dalam Proses (Masuk)
        $prosesQuery = BarangDalamProses::with('details');
        if ($request->start_date) {
            $prosesQuery->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $prosesQuery->whereDate('tanggal', '<=', $request->end_date);
        }
        if ($request->supplier) {
            $supplier = Supplier::find($request->supplier);
            if ($supplier) {
                $prosesQuery->where('nama_supplier', 'like', '%' . $supplier->nama_supplier . '%');
            }
        }
        if ($request->item) {
            $barang = Barang::find($request->item);
            if ($barang) {
                $prosesQuery->whereHas('details', function($q) use ($barang) {
                    $q->where('barang_kode', $barang->kode_barang);
                });
            }
        }
        if ($request->invoice_number) {
            $prosesQuery->where('nomor_faktur', 'like', '%' . $request->invoice_number . '%');
        }
        $proses = $prosesQuery->get();

        foreach ($proses as $p) {
            if ($p->details && $p->details->count() > 0) {
            foreach ($p->details as $detail) {
                    if ($detail) {
                $allTransactions->push([
                            'transaksi_id' => $p->id,
                            'transaksi_type' => 'barang_dalam_proses',
                    'tanggal' => $p->tanggal,
                            'nomor_invoice' => $p->nomor_faktur ?? '-',
                    'deskripsi' => 'Barang Dalam Proses',
                    'supplier' => $p->nama_supplier ?? '-',
                    'kode_supplier' => $p->kode_supplier ?? '-',
                    'item' => $detail->barang_nama ?? '-',
                    'kode_item' => $detail->barang_kode ?? '-',
                            'qty' => $detail->barang_qty ?? 0,
                            'unit' => $detail->barang_satuan ?? '-',
                    'harga' => $detail->barang_harga ?? 0,
                    'jumlah' => $detail->barang_jumlah ?? 0,
                ]);
                    }
                }
            }
        }

        // Get Barang Jadi (Masuk)
        $jadiQuery = BarangJadi::with('details');
        if ($request->start_date) {
            $jadiQuery->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $jadiQuery->whereDate('tanggal', '<=', $request->end_date);
        }
        if ($request->supplier) {
            $supplier = Supplier::find($request->supplier);
            if ($supplier) {
                $jadiQuery->where('nama_supplier', 'like', '%' . $supplier->nama_supplier . '%');
            }
        }
        if ($request->item) {
            $barang = Barang::find($request->item);
            if ($barang) {
                $jadiQuery->whereHas('details', function($q) use ($barang) {
                    $q->where('barang_kode', $barang->kode_barang);
                });
            }
        }
        if ($request->invoice_number) {
            $jadiQuery->where('nomor_faktur', 'like', '%' . $request->invoice_number . '%');
        }
        $jadis = $jadiQuery->get();

        foreach ($jadis as $jadi) {
            if ($jadi->details && $jadi->details->count() > 0) {
            foreach ($jadi->details as $detail) {
                    if ($detail) {
                $allTransactions->push([
                            'transaksi_id' => $jadi->id,
                            'transaksi_type' => 'barang_jadi',
                    'tanggal' => $jadi->tanggal,
                            'nomor_invoice' => $jadi->nomor_faktur ?? '-',
                    'deskripsi' => 'Barang Jadi',
                    'supplier' => $jadi->nama_supplier ?? '-',
                    'kode_supplier' => $jadi->kode_supplier ?? '-',
                    'item' => $detail->barang_nama ?? '-',
                    'kode_item' => $detail->barang_kode ?? '-',
                            'qty' => $detail->barang_qty ?? 0,
                            'unit' => $detail->barang_satuan ?? '-',
                    'harga' => $detail->barang_harga ?? 0,
                    'jumlah' => $detail->barang_jumlah ?? 0,
                ]);
                    }
                }
            }
        }

        // Get Pemakaian Bahan Baku (Keluar)
        $pemakaianBahanQuery = PemakaianBahanBaku::with(['details' => function($q) {
            $q->with('barang');
        }]);
        if ($request->start_date) {
            $pemakaianBahanQuery->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $pemakaianBahanQuery->whereDate('tanggal', '<=', $request->end_date);
        }
        if ($request->item) {
            $pemakaianBahanQuery->whereHas('details', function($q) use ($request) {
                $q->where('barang_id', $request->item);
            });
        }
        if ($request->invoice_number) {
            $pemakaianBahanQuery->where('nomor_bukti', 'like', '%' . $request->invoice_number . '%');
        }
        $pemakaianBahans = $pemakaianBahanQuery->get();

        foreach ($pemakaianBahans as $pemakaian) {
            if ($pemakaian->details && $pemakaian->details->count() > 0) {
            foreach ($pemakaian->details as $detail) {
                    if ($detail) {
                $allTransactions->push([
                            'transaksi_id' => $pemakaian->id,
                            'transaksi_type' => 'pemakaian_bahan_baku',
                    'tanggal' => $pemakaian->tanggal,
                            'nomor_invoice' => $pemakaian->nomor_bukti ?? '-',
                    'deskripsi' => 'Pemakaian Bahan Baku',
                    'supplier' => '-',
                    'kode_supplier' => '-',
                            'item' => $detail->barang->nama_barang ?? ($detail->barang_nama ?? '-'),
                            'kode_item' => $detail->barang->kode_barang ?? ($detail->barang_kode ?? '-'),
                            'qty' => $detail->quantity ?? 0,
                            'unit' => $detail->satuan ?? '-',
                    'harga' => $detail->harga ?? 0,
                    'jumlah' => $detail->jumlah ?? 0,
                ]);
                    }
                }
            }
        }

        // Get Pemakaian Barang Dalam Proses (Keluar)
        $pemakaianProsesQuery = PemakaianBarangDalamProses::with(['details' => function($q) {
            $q->with('barang');
        }]);
        if ($request->start_date) {
            $pemakaianProsesQuery->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $pemakaianProsesQuery->whereDate('tanggal', '<=', $request->end_date);
        }
        if ($request->item) {
            $pemakaianProsesQuery->whereHas('details', function($q) use ($request) {
                $q->where('barang_id', $request->item);
            });
        }
        if ($request->invoice_number) {
            $pemakaianProsesQuery->where('nomor_bukti', 'like', '%' . $request->invoice_number . '%');
        }
        $pemakaianProses = $pemakaianProsesQuery->get();

        foreach ($pemakaianProses as $pemakaian) {
            if ($pemakaian->details && $pemakaian->details->count() > 0) {
            foreach ($pemakaian->details as $detail) {
                    if ($detail) {
                $allTransactions->push([
                            'transaksi_id' => $pemakaian->id,
                            'transaksi_type' => 'pemakaian_barang_dalam_proses',
                    'tanggal' => $pemakaian->tanggal,
                            'nomor_invoice' => $pemakaian->nomor_bukti ?? '-',
                    'deskripsi' => 'Pemakaian Barang Dalam Proses',
                    'supplier' => '-',
                    'kode_supplier' => '-',
                            'item' => $detail->barang->nama_barang ?? ($detail->barang_nama ?? '-'),
                            'kode_item' => $detail->barang->kode_barang ?? ($detail->barang_kode ?? '-'),
                            'qty' => $detail->quantity ?? 0,
                            'unit' => $detail->satuan ?? '-',
                    'harga' => $detail->harga ?? 0,
                    'jumlah' => $detail->jumlah ?? 0,
                ]);
                    }
                }
            }
        }

        // Get Penjualan Barang Jadi (Keluar)
        $penjualanQuery = \App\Models\PenjualanBarangJadi::with(['details' => function($q) {
            $q->with('barang');
        }]);
        if ($request->start_date) {
            $penjualanQuery->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $penjualanQuery->whereDate('tanggal', '<=', $request->end_date);
        }
        if ($request->item) {
            $penjualanQuery->whereHas('details', function($q) use ($request) {
                $q->where('barang_id', $request->item);
            });
        }
        if ($request->invoice_number) {
            $penjualanQuery->where('nomor_bukti', 'like', '%' . $request->invoice_number . '%');
        }
        $penjualans = $penjualanQuery->get();

        foreach ($penjualans as $penjualan) {
            if ($penjualan->details && $penjualan->details->count() > 0) {
            foreach ($penjualan->details as $detail) {
                    if ($detail) {
                $allTransactions->push([
                            'transaksi_id' => $penjualan->id,
                            'transaksi_type' => 'penjualan_barang_jadi',
                    'tanggal' => $penjualan->tanggal,
                            'nomor_invoice' => $penjualan->nomor_bukti ?? '-',
                    'deskripsi' => 'Penjualan Barang Jadi',
                    'supplier' => '-',
                    'kode_supplier' => '-',
                            'item' => $detail->barang->nama_barang ?? ($detail->barang_nama ?? '-'),
                            'kode_item' => $detail->barang->kode_barang ?? ($detail->barang_kode ?? '-'),
                            'qty' => $detail->quantity ?? 0,
                            'unit' => $detail->satuan ?? '-',
                    'harga' => $detail->harga ?? 0,
                    'jumlah' => $detail->jumlah ?? 0,
                ]);
                    }
                }
            }
        }

        // Apply additional filtering on the final collection (in case some transactions weren't filtered at query level)
        if ($request->item) {
            $barang = Barang::find($request->item);
            if ($barang) {
                $allTransactions = $allTransactions->filter(function($transaction) use ($barang) {
                    return $transaction['kode_item'] == $barang->kode_barang || 
                           $transaction['item'] == $barang->nama_barang;
                });
            }
        }

        // Sort by date
        $data = $allTransactions->sortBy('tanggal')->values();
        $total = $data->sum('jumlah');

        $suppliers = Supplier::all();
        $barangs = Barang::orderBy('nama_barang')->get();

        // Handle export
        if ($request->export == 'pdf') {
            return $this->exportStockAkhirPDF($request);
        }
        if ($request->export == 'excel') {
            return $this->exportStockAkhirExcel($request);
        }

        return view('report.laporan-stock-akhir', compact('data', 'total', 'suppliers', 'barangs'));
    }

    /**
     * Delete transaction from Laporan Stock Akhir (Pimpinan only)
     */
    public function destroyStockAkhir(Request $request)
    {
        // Check if user is pimpinan (superadmin)
        if (!auth()->check() || auth()->user()->role !== 'superadmin') {
            abort(403, 'Unauthorized access. Only pimpinan can delete transactions.');
        }

        $transaksiId = $request->transaksi_id;
        $transaksiType = $request->transaksi_type;

        if (!$transaksiId || !$transaksiType) {
            return redirect()->back()->with('error', 'Data transaksi tidak valid.');
        }

        try {
            switch ($transaksiType) {
                case 'pembelian_bahan_baku':
                    $transaksi = PembelianBahanBaku::findOrFail($transaksiId);
                    $transaksi->delete();
                    break;

                case 'barang_dalam_proses':
                    $transaksi = BarangDalamProses::findOrFail($transaksiId);
                    $transaksi->delete();
                    break;

                case 'barang_jadi':
                    $transaksi = BarangJadi::findOrFail($transaksiId);
                    $transaksi->delete();
                    break;

                case 'pemakaian_bahan_baku':
                    $transaksi = PemakaianBahanBaku::findOrFail($transaksiId);
                    $transaksi->delete();
                    break;

                case 'pemakaian_barang_dalam_proses':
                    $transaksi = PemakaianBarangDalamProses::findOrFail($transaksiId);
                    $transaksi->delete();
                    break;

                case 'penjualan_barang_jadi':
                    $transaksi = \App\Models\PenjualanBarangJadi::findOrFail($transaksiId);
                    $transaksi->delete();
                    break;

                default:
                    return redirect()->back()->with('error', 'Jenis transaksi tidak valid.');
            }

            return redirect()->back()->with('success', 'Transaksi berhasil dihapus.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Transaksi tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Laporan Stock Opname
     */
    public function laporanStockOpname(Request $request)
    {
        // Get all barang with their current stock
        $barangs = Barang::all();
        
        $stockData = $barangs->map(function($barang) {
            // Get stok sistem from database
            $stokSistem = $barang->stok;
            
            // For demo, simulate stok fisik (in real app, this would come from opname input)
            // If there's a stock_opnames table, use that data
            $stokFisik = $stokSistem; // Default: same as sistem
            
            // Calculate selisih
            $selisih = $stokFisik - $stokSistem;

            return [
                'barang_id' => $barang->id,
                'kode' => $barang->kode_barang,
                'nama' => $barang->nama_barang,
                'kategori' => $barang->deskripsi ?? 'Umum',
                'satuan' => $barang->satuan,
                'stok_sistem' => $stokSistem,
                'stok_fisik' => $stokFisik,
                'selisih' => $selisih,
            ];
        });

        // Filter by kategori if specified
        if ($request->kategori) {
            $stockData = $stockData->filter(function($item) use ($request) {
                return stripos($item['kategori'], $request->kategori) !== false;
            });
        }

        // Sort
        $sortBy = $request->sort_by ?? 'nama';
        $sortOrder = $request->sort_order ?? 'asc';
        $stockData = $stockData->sortBy($sortBy, SORT_REGULAR, $sortOrder == 'desc')->values();

        return view('report.laporan-stock-opname', compact('stockData'));
    }

    /**
     * Delete barang from Laporan Stock Opname (Pimpinan only)
     */
    public function destroyStockOpnameBarang(Request $request)
    {
        // Check if user is pimpinan (superadmin)
        if (!auth()->check() || auth()->user()->role !== 'superadmin') {
            abort(403, 'Unauthorized access. Only pimpinan can delete barang.');
        }

        $barangId = $request->barang_id;

        if (!$barangId) {
            return redirect()->back()->with('error', 'ID barang tidak valid.');
        }

        try {
            $barang = Barang::findOrFail($barangId);
            
            // Check if barang is used in any transactions (comprehensive check)
            $usedInPembelian = \App\Models\PembelianBahanBakuDetail::where('barang_id', $barangId)->exists();
            $usedInPemakaianBahan = \App\Models\PemakaianBahanBakuDetail::where('barang_id', $barangId)->exists();
            $usedInPemakaianProses = \App\Models\PemakaianBarangDalamProsesDetail::where('barang_id', $barangId)->exists();
            $usedInPenjualan = \App\Models\PenjualanBarangJadiDetail::where('barang_id', $barangId)->exists();
            
            // Check in BarangDalamProsesDetail and BarangJadiDetail by kode_barang
            $usedInBarangDalamProses = \App\Models\BarangDalamProsesDetail::where('barang_kode', $barang->kode_barang)->exists();
            $usedInBarangJadi = \App\Models\BarangJadiDetail::where('barang_kode', $barang->kode_barang)->exists();
            
            if ($usedInPembelian || $usedInPemakaianBahan || $usedInPemakaianProses || $usedInPenjualan || $usedInBarangDalamProses || $usedInBarangJadi) {
                return redirect()->back()->with('error', 'Barang tidak dapat dihapus karena masih digunakan dalam transaksi.');
            }

            // Delete the barang
            $barang->delete();

            return redirect()->back()->with('success', 'Barang berhasil dihapus.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus barang: ' . $e->getMessage());
        }
    }

    /**
     * Delete Pembelian Bahan Baku (Admin and Pimpinan only)
     */
    public function destroyPembelianBahanBaku($id)
    {
        // Check if user has permission (admin or superadmin)
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Unauthorized access. Only admin and pimpinan can delete transactions.');
        }

        try {
            $pembelian = PembelianBahanBaku::with('details.barang')->findOrFail($id);
            
            // Reverse stock for each detail
            foreach ($pembelian->details as $detail) {
                if ($detail->barang) {
                    // Reduce stock by the quantity that was added
                    $detail->barang->reduceStock((int) round($detail->quantity));
                }
            }
            
            // Delete the pembelian (details will be deleted automatically due to cascade)
            $pembelian->delete();

            return redirect()->route('report.laporan-pembelian-bahan-baku')
                ->with('success', 'Data pembelian bahan baku berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('report.laporan-pembelian-bahan-baku')
                ->with('error', 'Gagal menghapus data pembelian bahan baku: ' . $e->getMessage());
        }
    }

    /**
     * Delete Pemakaian Barang Dalam Proses (Admin and Pimpinan only)
     */
    public function destroyPemakaianBarangDalamProses($id)
    {
        // Check if user has permission (admin or superadmin)
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Unauthorized access. Only admin and pimpinan can delete transactions.');
        }

        try {
            $pemakaian = PemakaianBarangDalamProses::with('details.barang')->findOrFail($id);
            
            // Reverse stock for each detail (add back the stock)
            foreach ($pemakaian->details as $detail) {
                if ($detail->barang) {
                    // Add back stock by the quantity that was reduced
                    $detail->barang->addStock((int) round($detail->quantity));
                }
            }
            
            // Delete the pemakaian (details will be deleted automatically due to cascade)
            $pemakaian->delete();

            return redirect()->route('report.laporan-pemakaian-barang-dalam-proses')
                ->with('success', 'Data pemakaian barang dalam proses berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('report.laporan-pemakaian-barang-dalam-proses')
                ->with('error', 'Gagal menghapus data pemakaian barang dalam proses: ' . $e->getMessage());
        }
    }

    /**
     * Delete Barang Dalam Proses (Admin and Pimpinan only)
     */
    public function destroyBarangDalamProses(Request $request)
    {
        // Check if user has permission (admin or superadmin)
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Unauthorized access. Only admin and pimpinan can delete transactions.');
        }

        $transaksiId = $request->transaksi_id;

        if (!$transaksiId) {
            return redirect()->back()->with('error', 'ID transaksi tidak valid.');
        }

        try {
            $barangDalamProses = BarangDalamProses::with('details')->findOrFail($transaksiId);
            
            // Reverse stock for each detail (reduce stock that was added)
            foreach ($barangDalamProses->details as $detail) {
                // Cari barang berdasarkan kode_barang
                $barang = Barang::where('kode_barang', $detail->barang_kode)->first();
                if ($barang) {
                    // Reduce stock by the quantity that was added
                    $barang->reduceStock((int) round($detail->barang_qty));
                }
            }
            
            // Delete the barang dalam proses (details will be deleted automatically due to cascade)
            $barangDalamProses->delete();

            return redirect()->route('report.laporan-barang-dalam-proses')
                ->with('success', 'Data barang dalam proses berhasil dihapus!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('report.laporan-barang-dalam-proses')
                ->with('error', 'Data barang dalam proses tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->route('report.laporan-barang-dalam-proses')
                ->with('error', 'Gagal menghapus data barang dalam proses: ' . $e->getMessage());
        }
    }

    /**
     * Helper method to get all stock akhir transactions data
     */
    private function getStockAkhirData(Request $request)
    {
        $allTransactions = collect([]);
        
        // Get Pembelian Bahan Baku (Masuk)
        $pembelianQuery = PembelianBahanBaku::with(['supplier', 'details.barang']);
        if ($request->start_date) {
            $pembelianQuery->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $pembelianQuery->whereDate('tanggal', '<=', $request->end_date);
        }
        if ($request->item) {
            $pembelianQuery->whereHas('details', function($q) use ($request) {
                $q->where('barang_id', $request->item);
            });
        }
        if ($request->invoice_number) {
            $pembelianQuery->where('nomor_faktur', 'like', '%' . $request->invoice_number . '%');
        }
        $pembelians = $pembelianQuery->get();

        foreach ($pembelians as $pembelian) {
            if ($pembelian->details && $pembelian->details->count() > 0) {
                foreach ($pembelian->details as $detail) {
                    if ($detail && $detail->barang) {
                        $allTransactions->push([
                            'tanggal' => $pembelian->tanggal,
                            'nomor_invoice' => $pembelian->nomor_faktur ?? '-',
                            'deskripsi' => 'Pembelian Bahan Baku',
                            'supplier' => $pembelian->supplier->nama_supplier ?? '-',
                            'kode_supplier' => $pembelian->supplier->kode_supplier ?? '-',
                            'item' => $detail->barang->nama_barang ?? '-',
                            'kode_item' => $detail->barang->kode_barang ?? '-',
                            'qty' => $detail->quantity ?? 0,
                            'unit' => $detail->satuan ?? '-',
                            'harga' => $detail->harga_beli ?? 0,
                            'jumlah' => $detail->jumlah ?? 0,
                        ]);
                    }
                }
            }
        }

        // Get Barang Dalam Proses (Masuk)
        $prosesQuery = BarangDalamProses::with('details');
        if ($request->start_date) {
            $prosesQuery->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $prosesQuery->whereDate('tanggal', '<=', $request->end_date);
        }
        if ($request->item) {
            $barang = Barang::find($request->item);
            if ($barang) {
                $prosesQuery->whereHas('details', function($q) use ($barang) {
                    $q->where('barang_kode', $barang->kode_barang);
                });
            }
        }
        if ($request->invoice_number) {
            $prosesQuery->where('nomor_faktur', 'like', '%' . $request->invoice_number . '%');
        }
        $proses = $prosesQuery->get();

        foreach ($proses as $p) {
            if ($p->details && $p->details->count() > 0) {
                foreach ($p->details as $detail) {
                    if ($detail) {
                        $allTransactions->push([
                            'tanggal' => $p->tanggal,
                            'nomor_invoice' => $p->nomor_faktur ?? '-',
                            'deskripsi' => 'Barang Dalam Proses',
                            'supplier' => $p->nama_supplier ?? '-',
                            'kode_supplier' => $p->kode_supplier ?? '-',
                            'item' => $detail->barang_nama ?? '-',
                            'kode_item' => $detail->barang_kode ?? '-',
                            'qty' => $detail->barang_qty ?? 0,
                            'unit' => $detail->barang_satuan ?? '-',
                            'harga' => $detail->barang_harga ?? 0,
                            'jumlah' => $detail->barang_jumlah ?? 0,
                        ]);
                    }
                }
            }
        }

        // Get Barang Jadi (Masuk)
        $jadiQuery = BarangJadi::with('details');
        if ($request->start_date) {
            $jadiQuery->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $jadiQuery->whereDate('tanggal', '<=', $request->end_date);
        }
        if ($request->item) {
            $barang = Barang::find($request->item);
            if ($barang) {
                $jadiQuery->whereHas('details', function($q) use ($barang) {
                    $q->where('barang_kode', $barang->kode_barang);
                });
            }
        }
        if ($request->invoice_number) {
            $jadiQuery->where('nomor_faktur', 'like', '%' . $request->invoice_number . '%');
        }
        $jadis = $jadiQuery->get();

        foreach ($jadis as $jadi) {
            if ($jadi->details && $jadi->details->count() > 0) {
                foreach ($jadi->details as $detail) {
                    if ($detail) {
                        $allTransactions->push([
                            'tanggal' => $jadi->tanggal,
                            'nomor_invoice' => $jadi->nomor_faktur ?? '-',
                            'deskripsi' => 'Barang Jadi',
                            'supplier' => $jadi->nama_supplier ?? '-',
                            'kode_supplier' => $jadi->kode_supplier ?? '-',
                            'item' => $detail->barang_nama ?? '-',
                            'kode_item' => $detail->barang_kode ?? '-',
                            'qty' => $detail->barang_qty ?? 0,
                            'unit' => $detail->barang_satuan ?? '-',
                            'harga' => $detail->barang_harga ?? 0,
                            'jumlah' => $detail->barang_jumlah ?? 0,
                        ]);
                    }
                }
            }
        }

        // Get Pemakaian Bahan Baku (Keluar)
        $pemakaianBahanQuery = PemakaianBahanBaku::with(['details' => function($q) {
            $q->with('barang');
        }]);
        if ($request->start_date) {
            $pemakaianBahanQuery->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $pemakaianBahanQuery->whereDate('tanggal', '<=', $request->end_date);
        }
        if ($request->item) {
            $pemakaianBahanQuery->whereHas('details', function($q) use ($request) {
                $q->where('barang_id', $request->item);
            });
        }
        if ($request->invoice_number) {
            $pemakaianBahanQuery->where('nomor_bukti', 'like', '%' . $request->invoice_number . '%');
        }
        $pemakaianBahans = $pemakaianBahanQuery->get();

        foreach ($pemakaianBahans as $pemakaian) {
            if ($pemakaian->details && $pemakaian->details->count() > 0) {
                foreach ($pemakaian->details as $detail) {
                    if ($detail) {
                        $allTransactions->push([
                            'tanggal' => $pemakaian->tanggal,
                            'nomor_invoice' => $pemakaian->nomor_bukti ?? '-',
                            'deskripsi' => 'Pemakaian Bahan Baku',
                            'supplier' => '-',
                            'kode_supplier' => '-',
                            'item' => $detail->barang->nama_barang ?? ($detail->barang_nama ?? '-'),
                            'kode_item' => $detail->barang->kode_barang ?? ($detail->barang_kode ?? '-'),
                            'qty' => $detail->quantity ?? 0,
                            'unit' => $detail->satuan ?? '-',
                            'harga' => $detail->harga ?? 0,
                            'jumlah' => $detail->jumlah ?? 0,
                        ]);
                    }
                }
            }
        }

        // Get Pemakaian Barang Dalam Proses (Keluar)
        $pemakaianProsesQuery = PemakaianBarangDalamProses::with(['details' => function($q) {
            $q->with('barang');
        }]);
        if ($request->start_date) {
            $pemakaianProsesQuery->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $pemakaianProsesQuery->whereDate('tanggal', '<=', $request->end_date);
        }
        if ($request->item) {
            $pemakaianProsesQuery->whereHas('details', function($q) use ($request) {
                $q->where('barang_id', $request->item);
            });
        }
        if ($request->invoice_number) {
            $pemakaianProsesQuery->where('nomor_bukti', 'like', '%' . $request->invoice_number . '%');
        }
        $pemakaianProses = $pemakaianProsesQuery->get();

        foreach ($pemakaianProses as $pemakaian) {
            if ($pemakaian->details && $pemakaian->details->count() > 0) {
                foreach ($pemakaian->details as $detail) {
                    if ($detail) {
                        $allTransactions->push([
                            'tanggal' => $pemakaian->tanggal,
                            'nomor_invoice' => $pemakaian->nomor_bukti ?? '-',
                            'deskripsi' => 'Pemakaian Barang Dalam Proses',
                            'supplier' => '-',
                            'kode_supplier' => '-',
                            'item' => $detail->barang->nama_barang ?? ($detail->barang_nama ?? '-'),
                            'kode_item' => $detail->barang->kode_barang ?? ($detail->barang_kode ?? '-'),
                            'qty' => $detail->quantity ?? 0,
                            'unit' => $detail->satuan ?? '-',
                            'harga' => $detail->harga ?? 0,
                            'jumlah' => $detail->jumlah ?? 0,
                        ]);
                    }
                }
            }
        }

        // Get Penjualan Barang Jadi (Keluar)
        $penjualanQuery = \App\Models\PenjualanBarangJadi::with(['details' => function($q) {
            $q->with('barang');
        }]);
        if ($request->start_date) {
            $penjualanQuery->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $penjualanQuery->whereDate('tanggal', '<=', $request->end_date);
        }
        if ($request->item) {
            $penjualanQuery->whereHas('details', function($q) use ($request) {
                $q->where('barang_id', $request->item);
            });
        }
        if ($request->invoice_number) {
            $penjualanQuery->where('nomor_bukti', 'like', '%' . $request->invoice_number . '%');
        }
        $penjualans = $penjualanQuery->get();

        foreach ($penjualans as $penjualan) {
            if ($penjualan->details && $penjualan->details->count() > 0) {
                foreach ($penjualan->details as $detail) {
                    if ($detail) {
                        $allTransactions->push([
                            'tanggal' => $penjualan->tanggal,
                            'nomor_invoice' => $penjualan->nomor_bukti ?? '-',
                            'deskripsi' => 'Penjualan Barang Jadi',
                            'supplier' => '-',
                            'kode_supplier' => '-',
                            'item' => $detail->barang->nama_barang ?? ($detail->barang_nama ?? '-'),
                            'kode_item' => $detail->barang->kode_barang ?? ($detail->barang_kode ?? '-'),
                            'qty' => $detail->quantity ?? 0,
                            'unit' => $detail->satuan ?? '-',
                            'harga' => $detail->harga ?? 0,
                            'jumlah' => $detail->jumlah ?? 0,
                        ]);
                    }
                }
            }
        }

        // Apply additional filtering
        if ($request->item) {
            $barang = Barang::find($request->item);
            if ($barang) {
                $allTransactions = $allTransactions->filter(function($transaction) use ($barang) {
                    return $transaction['kode_item'] == $barang->kode_barang || 
                           $transaction['item'] == $barang->nama_barang;
                });
            }
        }

        return $allTransactions->sortBy('tanggal')->values();
    }

    /**
     * Export Laporan Stock Akhir to PDF
     */
    public function exportStockAkhirPDF(Request $request)
    {
        $data = $this->getStockAkhirData($request);
        $total = $data->sum('jumlah');

        $pdf = Pdf::loadView('report.export.stock-akhir-pdf', [
            'data' => $data,
            'total' => $total,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-stock-akhir-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export Laporan Stock Akhir to Excel
     */
    public function exportStockAkhirExcel(Request $request)
    {
        $data = $this->getStockAkhirData($request);
        $total = $data->sum('jumlah');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
        $sheet->setCellValue('A1', 'LAPORAN STOCK AKHIR - SAE BAKERY');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A2', 'Periode: ' . ($request->start_date ?? 'Semua') . ' - ' . ($request->end_date ?? 'Semua'));
        $sheet->mergeCells('A2:K2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Table headers
        $headers = ['Tanggal', 'Nomor Invoice', 'Deskripsi', 'Supplier', 'Kode Supplier', 'Item', 'Kode Item', 'Qty', 'Unit', 'Harga', 'Jumlah'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $sheet->getStyle($col . '4')->getFont()->setBold(true);
            $sheet->getStyle($col . '4')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('D4AF37');
            $col++;
        }
        
        // Data rows
        $row = 5;
        foreach ($data as $item) {
            $tanggal = $item['tanggal'];
            if ($tanggal instanceof \Carbon\Carbon) {
                $tanggal = $tanggal->format('Y-m-d');
            }
            $sheet->setCellValue('A' . $row, $tanggal);
            $sheet->setCellValue('B' . $row, $item['nomor_invoice']);
            $sheet->setCellValue('C' . $row, $item['deskripsi']);
            $sheet->setCellValue('D' . $row, $item['supplier']);
            $sheet->setCellValue('E' . $row, $item['kode_supplier']);
            $sheet->setCellValue('F' . $row, $item['item']);
            $sheet->setCellValue('G' . $row, $item['kode_item']);
            $sheet->setCellValue('H' . $row, $item['qty']);
            $sheet->setCellValue('I' . $row, $item['unit']);
            $sheet->setCellValue('J' . $row, $item['harga']);
            $sheet->setCellValue('K' . $row, $item['jumlah']);
            $row++;
        }
        
        // Total
        $sheet->setCellValue('J' . $row, 'TOTAL:');
        $sheet->setCellValue('K' . $row, $total);
        $sheet->getStyle('J' . $row . ':K' . $row)->getFont()->setBold(true);
        
        // Auto size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan-stock-akhir-' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Export Kartu Stock to PDF
     */
    public function exportKartuStockPDF(Request $request, $barang, $transactionsMasuk, $transactionsKeluar, $initialBalance, $initialStockValue, $totalMasukValue, $totalKeluarValue, $netProfit)
    {
        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        $pdf = Pdf::loadView('report.export.kartu-stock-pdf', [
            'barang' => $barang,
            'transactionsMasuk' => $transactionsMasuk ?? collect([]),
            'transactionsKeluar' => $transactionsKeluar ?? collect([]),
            'initialBalance' => $initialBalance ?? 0,
            'initialStockValue' => $initialStockValue ?? 0,
            'totalMasukValue' => $totalMasukValue ?? 0,
            'totalKeluarValue' => $totalKeluarValue ?? 0,
            'netProfit' => $netProfit ?? 0,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('kartu-stock-' . $barang->kode_barang . '-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export Kartu Stock to Excel
     */
    public function exportKartuStockExcel(Request $request, $barang, $transactionsMasuk, $transactionsKeluar, $initialBalance, $initialStockValue, $totalMasukValue, $totalKeluarValue, $netProfit)
    {
        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
        $sheet->setCellValue('A1', 'KARTU STOCK - SAE BAKERY');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A2', 'Item: ' . $barang->nama_barang . ' (' . $barang->kode_barang . ')');
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A3', 'Periode: ' . ($request->start_date ?? 'Semua') . ' - ' . ($request->end_date ?? 'Semua'));
        $sheet->mergeCells('A3:F3');
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $row = 5;
        
        // Items In Section
        $sheet->setCellValue('A' . $row, 'ITEMS IN');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D4EDDA');
        $row++;
        
        $headersMasuk = ['Tanggal', 'Invoice', 'Supplier', 'Qty', 'Unit Price', 'Amount'];
        $col = 'A';
        foreach ($headersMasuk as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $col++;
        }
        $row++;
        
        foreach ($transactionsMasuk ?? [] as $item) {
            $tanggal = $item['tanggal'];
            if ($tanggal instanceof \Carbon\Carbon) {
                $tanggal = $tanggal->format('Y-m-d');
            }
            $sheet->setCellValue('A' . $row, $tanggal);
            $sheet->setCellValue('B' . $row, $item['nomor'] ?? '-');
            $sheet->setCellValue('C' . $row, $item['supplier'] ?? '-');
            $sheet->setCellValue('D' . $row, $item['quantity'] ?? 0);
            $sheet->setCellValue('E' . $row, $item['unit_price'] ?? 0);
            $sheet->setCellValue('F' . $row, $item['amount'] ?? 0);
            $row++;
        }
        
        $sheet->setCellValue('E' . $row, 'Total:');
        $sheet->setCellValue('F' . $row, $totalMasukValue ?? 0);
        $sheet->getStyle('E' . $row . ':F' . $row)->getFont()->setBold(true);
        $row += 2;
        
        // Items Out Section
        $sheet->setCellValue('A' . $row, 'ITEMS OUT');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F8D7DA');
        $row++;
        
        $headersKeluar = ['Tanggal', 'Receipt', 'Customer', 'Qty', 'Unit Price', 'Amount'];
        $col = 'A';
        foreach ($headersKeluar as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $col++;
        }
        $row++;
        
        foreach ($transactionsKeluar ?? [] as $item) {
            $tanggal = $item['tanggal'];
            if ($tanggal instanceof \Carbon\Carbon) {
                $tanggal = $tanggal->format('Y-m-d');
            }
            $sheet->setCellValue('A' . $row, $tanggal);
            $sheet->setCellValue('B' . $row, $item['nomor'] ?? '-');
            $sheet->setCellValue('C' . $row, $item['customer'] ?? '-');
            $sheet->setCellValue('D' . $row, $item['quantity'] ?? 0);
            $sheet->setCellValue('E' . $row, $item['unit_price'] ?? 0);
            $sheet->setCellValue('F' . $row, $item['amount'] ?? 0);
            $row++;
        }
        
        $sheet->setCellValue('E' . $row, 'Total:');
        $sheet->setCellValue('F' . $row, $totalKeluarValue ?? 0);
        $sheet->getStyle('E' . $row . ':F' . $row)->getFont()->setBold(true);
        $row += 2;
        
        // Summary
        $sheet->setCellValue('A' . $row, 'Summary');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Initial Balance:');
        $sheet->setCellValue('B' . $row, $initialBalance ?? 0);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Current Stock:');
        $sheet->setCellValue('B' . $row, $barang->stok ?? 0);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Net Profit:');
        $sheet->setCellValue('B' . $row, $netProfit ?? 0);
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
        
        // Auto size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'kartu-stock-' . $barang->kode_barang . '-' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Export Laporan Pembelian Bahan Baku to PDF
     */
    public function exportPembelianBahanBakuPDF(Request $request, $data, $total)
    {
        $pdf = Pdf::loadView('report.export.pembelian-bahan-baku-pdf', [
            'data' => $data,
            'total' => $total,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-pembelian-bahan-baku-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export Laporan Pembelian Bahan Baku to Excel
     */
    public function exportPembelianBahanBakuExcel(Request $request, $data, $total)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'LAPORAN PEMBELIAN BAHAN BAKU - SAE BAKERY');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A2', 'Periode: ' . ($request->start_date ?? 'Semua') . ' - ' . ($request->end_date ?? 'Semua'));
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $headers = ['Tanggal', 'Nomor Faktur', 'Supplier', 'Kode Supplier', 'Barang', 'Qty', 'Satuan', 'Total Harga'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $sheet->getStyle($col . '4')->getFont()->setBold(true);
            $sheet->getStyle($col . '4')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('D4AF37');
            $col++;
        }
        
        $row = 5;
        foreach ($data as $pembelian) {
            foreach ($pembelian->details as $detail) {
                $sheet->setCellValue('A' . $row, $pembelian->tanggal->format('Y-m-d'));
                $sheet->setCellValue('B' . $row, $pembelian->nomor_faktur);
                $sheet->setCellValue('C' . $row, $pembelian->supplier->nama_supplier ?? '-');
                $sheet->setCellValue('D' . $row, $pembelian->supplier->kode_supplier ?? '-');
                $sheet->setCellValue('E' . $row, $detail->barang->nama_barang ?? '-');
                $sheet->setCellValue('F' . $row, $detail->quantity);
                $sheet->setCellValue('G' . $row, $detail->satuan);
                $sheet->setCellValue('H' . $row, $detail->jumlah);
                $row++;
            }
        }
        
        $sheet->setCellValue('G' . $row, 'TOTAL:');
        $sheet->setCellValue('H' . $row, $total);
        $sheet->getStyle('G' . $row . ':H' . $row)->getFont()->setBold(true);
        
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan-pembelian-bahan-baku-' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Export Laporan Barang Dalam Proses to PDF
     */
    public function exportBarangDalamProsesPDF(Request $request, $data, $total)
    {
        $pdf = Pdf::loadView('report.export.barang-dalam-proses-pdf', [
            'data' => $data,
            'total' => $total,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-barang-dalam-proses-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export Laporan Barang Dalam Proses to Excel
     */
    public function exportBarangDalamProsesExcel(Request $request, $data, $total)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'LAPORAN BARANG DALAM PROSES - SAE BAKERY');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A2', 'Periode: ' . ($request->start_date ?? 'Semua') . ' - ' . ($request->end_date ?? 'Semua'));
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $headers = ['Tanggal', 'Nomor Faktur', 'Barang', 'Qty', 'Satuan', 'Harga', 'Total'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $sheet->getStyle($col . '4')->getFont()->setBold(true);
            $sheet->getStyle($col . '4')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('D4AF37');
            $col++;
        }
        
        $row = 5;
        foreach ($data as $item) {
            $tanggal = $item['tanggal'];
            if ($tanggal instanceof \Carbon\Carbon) {
                $tanggal = $tanggal->format('Y-m-d');
            }
            $sheet->setCellValue('A' . $row, $tanggal);
            $sheet->setCellValue('B' . $row, $item['nomor_faktur'] ?? '-');
            $sheet->setCellValue('C' . $row, $item['barang'] ?? '-');
            $sheet->setCellValue('D' . $row, $item['qty'] ?? 0);
            $sheet->setCellValue('E' . $row, $item['satuan'] ?? '-');
            $sheet->setCellValue('F' . $row, $item['harga'] ?? 0);
            $sheet->setCellValue('G' . $row, $item['total'] ?? 0);
            $row++;
        }
        
        $sheet->setCellValue('F' . $row, 'TOTAL:');
        $sheet->setCellValue('G' . $row, $total);
        $sheet->getStyle('F' . $row . ':G' . $row)->getFont()->setBold(true);
        
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan-barang-dalam-proses-' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Export Laporan Penjualan to PDF
     */
    public function exportPenjualanPDF(Request $request, $data, $total)
    {
        $pdf = Pdf::loadView('report.export.penjualan-pdf', [
            'data' => $data,
            'total' => $total,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-penjualan-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export Laporan Penjualan to Excel
     */
    public function exportPenjualanExcel(Request $request, $data, $total)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'LAPORAN PENJUALAN - SAE BAKERY');
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A2', 'Periode: ' . ($request->start_date ?? 'Semua') . ' - ' . ($request->end_date ?? 'Semua'));
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $headers = ['Tanggal', 'Nomor Bukti', 'Keterangan', 'Customer', 'Kode Customer', 'Item', 'Kode Item', 'Qty', 'Unit', 'Jumlah'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $sheet->getStyle($col . '4')->getFont()->setBold(true);
            $sheet->getStyle($col . '4')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('D4AF37');
            $col++;
        }
        
        $row = 5;
        foreach ($data as $item) {
            $tanggal = $item['tanggal'];
            if ($tanggal instanceof \Carbon\Carbon) {
                $tanggal = $tanggal->format('Y-m-d');
            }
            $sheet->setCellValue('A' . $row, $tanggal);
            $sheet->setCellValue('B' . $row, $item['nomor_bukti'] ?? '-');
            $sheet->setCellValue('C' . $row, $item['keterangan'] ?? '-');
            $sheet->setCellValue('D' . $row, $item['customer'] ?? '-');
            $sheet->setCellValue('E' . $row, $item['kode_customer'] ?? '-');
            $sheet->setCellValue('F' . $row, $item['item'] ?? '-');
            $sheet->setCellValue('G' . $row, $item['kode_item'] ?? '-');
            $sheet->setCellValue('H' . $row, $item['qty'] ?? 0);
            $sheet->setCellValue('I' . $row, $item['unit'] ?? '-');
            $sheet->setCellValue('J' . $row, $item['jumlah'] ?? 0);
            $row++;
        }
        
        $sheet->setCellValue('I' . $row, 'TOTAL:');
        $sheet->setCellValue('J' . $row, $total);
        $sheet->getStyle('I' . $row . ':J' . $row)->getFont()->setBold(true);
        
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan-penjualan-' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Export Laporan Pemakaian Bahan Baku to PDF
     */
    public function exportPemakaianBahanBakuPDF(Request $request, $data, $total)
    {
        $pdf = Pdf::loadView('report.export.pemakaian-bahan-baku-pdf', [
            'data' => $data,
            'total' => $total,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-pemakaian-bahan-baku-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export Laporan Pemakaian Bahan Baku to Excel
     */
    public function exportPemakaianBahanBakuExcel(Request $request, $data, $total)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'LAPORAN PEMAKAIAN BAHAN BAKU - SAE BAKERY');
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A2', 'Periode: ' . ($request->start_date ?? 'Semua') . ' - ' . ($request->end_date ?? 'Semua'));
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $headers = ['Tanggal', 'Nomor Bukti', 'Keterangan', 'Customer', 'Kode Customer', 'Item', 'Kode Item', 'Qty', 'Unit', 'Jumlah'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $sheet->getStyle($col . '4')->getFont()->setBold(true);
            $sheet->getStyle($col . '4')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('D4AF37');
            $col++;
        }
        
        $row = 5;
        foreach ($data as $item) {
            $tanggal = $item['tanggal'];
            if ($tanggal instanceof \Carbon\Carbon) {
                $tanggal = $tanggal->format('Y-m-d');
            }
            $sheet->setCellValue('A' . $row, $tanggal);
            $sheet->setCellValue('B' . $row, $item['nomor_bukti'] ?? '-');
            $sheet->setCellValue('C' . $row, $item['keterangan'] ?? '-');
            $sheet->setCellValue('D' . $row, $item['customer'] ?? '-');
            $sheet->setCellValue('E' . $row, $item['kode_customer'] ?? '-');
            $sheet->setCellValue('F' . $row, $item['item'] ?? '-');
            $sheet->setCellValue('G' . $row, $item['kode_item'] ?? '-');
            $sheet->setCellValue('H' . $row, $item['qty'] ?? 0);
            $sheet->setCellValue('I' . $row, $item['unit'] ?? '-');
            $sheet->setCellValue('J' . $row, $item['jumlah'] ?? 0);
            $row++;
        }
        
        $sheet->setCellValue('I' . $row, 'TOTAL:');
        $sheet->setCellValue('J' . $row, $total);
        $sheet->getStyle('I' . $row . ':J' . $row)->getFont()->setBold(true);
        
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan-pemakaian-bahan-baku-' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Export Laporan Data Barang to PDF
     */
    public function exportDataBarangPDF(Request $request, $data)
    {
        $pdf = Pdf::loadView('report.export.data-barang-pdf', [
            'data' => $data,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-data-barang-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export Laporan Data Barang to Excel
     */
    public function exportDataBarangExcel(Request $request, $data)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'LAPORAN DATA BARANG - SAE BAKERY');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $headers = ['Kode Barang', 'Nama Barang', 'Kategori', 'Satuan', 'Stok', 'Harga Beli', 'Limit Stock'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $sheet->getStyle($col . '3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('D4AF37');
            $col++;
        }
        
        $row = 4;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->kode_barang);
            $sheet->setCellValue('B' . $row, $item->nama_barang);
            $sheet->setCellValue('C' . $row, $item->deskripsi ?? '-');
            $sheet->setCellValue('D' . $row, $item->satuan);
            $sheet->setCellValue('E' . $row, $item->stok);
            $sheet->setCellValue('F' . $row, $item->harga_beli ?? 0);
            $sheet->setCellValue('G' . $row, $item->limit_stock ?? 0);
            $row++;
        }
        
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan-data-barang-' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Export Laporan Data Customer to PDF
     */
    public function exportDataCustomerPDF(Request $request, $data)
    {
        $pdf = Pdf::loadView('report.export.data-customer-pdf', [
            'data' => $data,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-data-customer-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export Laporan Data Customer to Excel
     */
    public function exportDataCustomerExcel(Request $request, $data)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'LAPORAN DATA CUSTOMER - SAE BAKERY');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $headers = ['Kode Customer', 'Nama Customer', 'Alamat', 'Telepon', 'Email', 'Tipe Customer', 'Keterangan'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $sheet->getStyle($col . '3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('D4AF37');
            $col++;
        }
        
        $row = 4;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->kode_customer);
            $sheet->setCellValue('B' . $row, $item->nama_customer);
            $sheet->setCellValue('C' . $row, $item->alamat ?? '-');
            $sheet->setCellValue('D' . $row, $item->telepon ?? '-');
            $sheet->setCellValue('E' . $row, $item->email ?? '-');
            $sheet->setCellValue('F' . $row, $item->tipe_customer ?? '-');
            $sheet->setCellValue('G' . $row, $item->keterangan ?? '-');
            $row++;
        }
        
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan-data-customer-' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Export Laporan Data Supplier to PDF
     */
    public function exportDataSupplierPDF(Request $request, $data)
    {
        $pdf = Pdf::loadView('report.export.data-supplier-pdf', [
            'data' => $data,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-data-supplier-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export Laporan Data Supplier to Excel
     */
    public function exportDataSupplierExcel(Request $request, $data)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'LAPORAN DATA SUPPLIER - SAE BAKERY');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $headers = ['Kode Supplier', 'Nama Supplier', 'Alamat', 'Telepon', 'Email', 'Nama Pemilik', 'Keterangan'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $sheet->getStyle($col . '3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('D4AF37');
            $col++;
        }
        
        $row = 4;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->kode_supplier);
            $sheet->setCellValue('B' . $row, $item->nama_supplier);
            $sheet->setCellValue('C' . $row, $item->alamat ?? '-');
            $sheet->setCellValue('D' . $row, $item->telepon ?? '-');
            $sheet->setCellValue('E' . $row, $item->email ?? '-');
            $sheet->setCellValue('F' . $row, $item->nama_pemilik ?? '-');
            $sheet->setCellValue('G' . $row, $item->keterangan ?? '-');
            $row++;
        }
        
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan-data-supplier-' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
