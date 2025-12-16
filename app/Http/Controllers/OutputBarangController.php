<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PemakaianBarangDalamProses;
use App\Models\PemakaianBarangDalamProsesDetail;
use App\Models\PemakaianBahanBaku;
use App\Models\PemakaianBahanBakuDetail;
use App\Models\PenjualanBarangJadi;
use App\Models\PenjualanBarangJadiDetail;
use App\Models\Customer;
use App\Http\Requests\PenjualanBarangJadiRequest;
use App\Http\Requests\PemakaianBahanBakuRequest;
use App\Http\Requests\PemakaianBarangDalamProsesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\InventoryService;

class OutputBarangController extends Controller
{
    /**
     * Show form Penjualan Barang Jadi
     */
    public function penjualanBarangJadi()
    {
        // No need to preload data - autocomplete will fetch dynamically
        return view('output-barang.penjualan-barang-jadi');
    }

    /**
     * API: Search customers by term
     */
    public function searchCustomer(Request $request)
    {
        $term = $request->input('term', '');
        
        if (empty($term)) {
            return response()->json([]);
        }
        
        $customers = Customer::where('nama_customer', 'like', '%' . $term . '%')
            ->orWhere('kode_customer', 'like', '%' . $term . '%')
            ->select('id', 'nama_customer', 'kode_customer', 'telepon', 'alamat')
            ->limit(10)
            ->get();
        
        return response()->json($customers);
    }

    /**
     * API: Search barang for penjualan (includes harga_jual)
     */
    public function searchBarangPenjualan(Request $request)
    {
        $term = $request->input('term', '');
        
        if (empty($term)) {
            return response()->json([]);
        }
        
        $barangs = Barang::where('nama_barang', 'like', '%' . $term . '%')
            ->orWhere('kode_barang', 'like', '%' . $term . '%')
            ->select('id', 'nama_barang', 'kode_barang', 'satuan', 'harga_jual')
            ->limit(10)
            ->get()
            ->map(function($barang) {
                return [
                    'id' => $barang->id,
                    'nama_barang' => $barang->nama_barang,
                    'kode_barang' => $barang->kode_barang,
                    'satuan' => $barang->satuan,
                    'harga_jual_default' => $barang->harga_jual ?? 0,
                ];
            });
        
        return response()->json($barangs);
    }

    /**
     * Store Penjualan Barang Jadi
     */
    public function storePenjualanBarangJadi(PenjualanBarangJadiRequest $request, InventoryService $inventoryService)
    {
        // Semua request sudah tervalidasi otomatis oleh PenjualanBarangJadiRequest
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Ambil data customer berdasarkan nama (auto kode)
            $customer = Customer::where('nama_customer', $validated['nama_customer'])->first();

            if (!$customer) {
                return back()->withErrors(['nama_customer' => 'Customer tidak ditemukan.'])->withInput();
            }

            // Pastikan kode customer otomatis benar
            $kodeCustomer = $customer->kode_customer;

            // Validate stock availability before processing
            $stockErrors = [];
            foreach ($validated['barang_kode'] as $index => $barangKode) {
                $quantity = (float) ($validated['barang_qty'][$index] ?? 0);
                
                // Ambil barang dari master
                $barang = Barang::where('kode_barang', $barangKode)->first();
                
                if (!$barang) {
                    $stockErrors[] = "Barang dengan kode {$barangKode} tidak ditemukan";
                    continue;
                }
                
                // Validasi kategori: hanya "Barang Jadi" yang diperbolehkan
                $kategori = strtolower(trim($barang->deskripsi ?? ''));
                $allowedKategori = ['barang jadi', 'barang_jadi', 'barangjadi'];
                
                // Normalize check
                $normalizedKategori = str_replace('_', ' ', $kategori);
                $normalizedAllowed = array_map(function($k) {
                    return str_replace('_', ' ', strtolower($k));
                }, $allowedKategori);

                if (!in_array($normalizedKategori, $normalizedAllowed) && !in_array($kategori, $allowedKategori)) {
                    $stockErrors[] = "Barang '{$barang->nama_barang}' bukan kategori Barang Jadi. Hanya barang dengan kategori 'Barang Jadi' yang dapat digunakan untuk penjualan barang jadi.";
                    continue;
                }

                if ($barang->stok < $quantity) {
                    $stockErrors[] = "Stok {$barang->nama_barang} tidak mencukupi. Stok tersedia: {$barang->stok}, dibutuhkan: {$quantity}";
                }
            }

            if (!empty($stockErrors)) {
                throw new \Exception(implode(', ', $stockErrors));
            }

            // Process output and reduce stock
            $totalHarga = 0;
            foreach ($validated['barang_kode'] as $index => $barangKode) {
                $namaBarang = $validated['barang_nama'][$index] ?? '';
                $quantity = (float) ($validated['barang_qty'][$index] ?? 0);
                $satuan = $validated['barang_satuan'][$index] ?? '';

                // Ambil barang dari master
                $barang = Barang::where('kode_barang', $barangKode)->first();
                if (!$barang) {
                    return back()->withErrors(['barang_kode.' . $index => 'Barang tidak ada di master data'])->withInput();
                }

                // Use Master Data Price
                $hargaJual = $barang->harga_jual;
                $jumlah = $quantity * $hargaJual;
                
                // Validasi kategori: hanya "Barang Jadi" yang diperbolehkan
                $kategori = strtolower(trim($barang->deskripsi ?? ''));
                $allowedKategori = ['barang jadi', 'barang_jadi', 'barangjadi'];
                
                // Normalize check
                $normalizedKategori = str_replace('_', ' ', $kategori);
                $normalizedAllowed = array_map(function($k) {
                    return str_replace('_', ' ', strtolower($k));
                }, $allowedKategori);

                if (!in_array($normalizedKategori, $normalizedAllowed) && !in_array($kategori, $allowedKategori)) {
                    throw new \Exception("Barang '{$barang->nama_barang}' bukan kategori Barang Jadi. Hanya barang dengan kategori 'Barang Jadi' yang dapat digunakan untuk penjualan barang jadi.");
                }

                // Validate quantity
                if ($quantity <= 0) {
                    throw new \Exception('Quantity harus lebih dari 0');
                }

                // Validate stock again (double check)
                if ($barang->stok < $quantity) {
                    throw new \Exception("Stok {$barang->nama_barang} tidak mencukupi. Stok tersedia: {$barang->stok}, dibutuhkan: {$quantity}");
                }

                // Validate stock availability
                if (!$barang->hasStock($quantity)) {
                    throw new \Exception("Stok {$barang->nama_barang} tidak mencukupi");
                }

                // Reduce stock: stok_akhir = stok_awal - keluar
                $barang->reduceStock($quantity);
                
                // Ensure stock doesn't go negative
                $barang->refresh();
                if ($barang->stok < 0) {
                    $barang->increment('stok', $quantity);
                    throw new \Exception("Stok {$barang->nama_barang} tidak boleh negatif");
                }

                // JANGAN update harga_jual di master (Input Saldo Awal is the only place)

                $totalHarga += $jumlah;
            }

            // Create penjualan transaction record
            $penjualan = PenjualanBarangJadi::create([
                'tanggal' => $validated['tanggal'],
                'nomor_bukti' => $validated['nomor_bukti'],
                'keterangan' => $validated['keterangan'] ?? '',
                'nama_customer' => $customer->nama_customer,
                'kode_customer' => $kodeCustomer,
                'total_harga' => $totalHarga,
            ]);

            // Create penjualan details
            foreach ($validated['barang_kode'] as $index => $barangKode) {
                $namaBarang = $validated['barang_nama'][$index] ?? '';
                $quantity = (float) ($validated['barang_qty'][$index] ?? 0);
                $satuan = $validated['barang_satuan'][$index] ?? '';

                // Ambil barang dari master (untuk konsistensi harga)
                $barang = Barang::where('kode_barang', $barangKode)->first();
                if ($barang) {
                    // Re-calculate based on master price
                    $hargaJual = $barang->harga_jual;
                    $jumlah = $quantity * $hargaJual;

                    // Consume Inventory (FIFO/LIFO/AVG)
                    $costData = $inventoryService->consumeInventory($barang, $quantity);

                    PenjualanBarangJadiDetail::create([
                        'penjualan_barang_jadi_id' => $penjualan->id,
                        'barang_id' => $barang->id,
                        'barang_nama' => $barang->nama_barang,
                        'barang_kode' => $barang->kode_barang,
                        'quantity' => $quantity,
                        'satuan' => $barang->satuan,
                        'harga' => $hargaJual,
                        'jumlah' => $jumlah,
                        'hpp_total' => $costData['total_cost'],
                        'hpp_unit' => $costData['unit_cost'],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('output-barang.penjualan-barang-jadi')
                ->with('success', 'Data penjualan barang jadi berhasil disimpan! Total: Rp ' . number_format($totalHarga, 0, ',', '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show form Pemakaian Bahan Baku
     */
    public function pemakaianBahanBaku()
    {
        $barangs = Barang::all();
        return view('output-barang.pemakaian-bahan-baku', compact('barangs'));
    }

    /**
     * Store Pemakaian Bahan Baku
     */
    public function storePemakaianBahanBaku(PemakaianBahanBakuRequest $request, InventoryService $inventoryService)
    {
        // Semua request sudah tervalidasi otomatis oleh PemakaianBahanBakuRequest
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Validate stock availability first
            $stockErrors = [];
            foreach ($validated['barang_kode'] as $index => $barangKode) {
                $quantity = (float) ($validated['barang_qty'][$index] ?? 0);
                
                // Ambil barang dari master
                $barang = Barang::where('kode_barang', $barangKode)->first();
                
                if (!$barang) {
                    $stockErrors[] = "Barang dengan kode {$barangKode} tidak ditemukan";
                    continue;
                }

                if ($barang->stok < $quantity) {
                    $stockErrors[] = "Stok {$barang->nama_barang} tidak mencukupi. Stok tersedia: {$barang->stok}, dibutuhkan: {$quantity}";
                }
            }

            if (!empty($stockErrors)) {
                throw new \Exception(implode(', ', $stockErrors));
            }

            // Ambil customer dari master data berdasarkan nama
            $customer = Customer::where('nama_customer', $validated['nama_customer'])->first();
            
            if (!$customer) {
                return back()->withErrors(['nama_customer' => 'Customer tidak ditemukan.'])->withInput();
            }

            // Pastikan kode customer otomatis benar
            $kodeCustomer = $customer->kode_customer;

            // Create header
            $pemakaian = PemakaianBahanBaku::create([
                'tanggal' => $validated['tanggal'],
                'nomor_bukti' => $validated['nomor_bukti'],
                'keterangan' => $validated['keterangan'] ?? '',
                'nama_customer' => $customer->nama_customer, // Nama Departemen/Produksi - dari master customer
                'kode_customer' => $kodeCustomer, // Kode Referensi - auto dari master customer
                'total_harga' => 0,
            ]);

            // Create details and reduce stock
            $totalHarga = 0;
            foreach ($validated['barang_kode'] as $index => $barangKode) {
                // Ambil barang dari master berdasarkan kode
                $barang = Barang::where('kode_barang', $barangKode)->first();
                if (!$barang) {
                    return back()->withErrors([
                        'barang_kode.' . $index => 'Barang dengan kode "' . $barangKode . '" tidak ditemukan di master.'
                    ])->withInput();
                }
                
                // Validasi kategori: hanya "Bahan Baku" yang diperbolehkan
                $kategori = strtolower(trim($barang->deskripsi ?? ''));
                $allowedKategori = ['bahan baku', 'bahan_baku', 'bahanbaku', 'bahan roti', 'bahan kopi'];
                
                // Normalize check
                $normalizedKategori = str_replace('_', ' ', $kategori);
                $normalizedAllowed = array_map(function($k) {
                    return str_replace('_', ' ', strtolower($k));
                }, $allowedKategori);

                if (!in_array($normalizedKategori, $normalizedAllowed) && !in_array($kategori, $allowedKategori)) {
                    throw new \Exception("Barang '{$barang->nama_barang}' bukan kategori Bahan Baku. Hanya barang dengan kategori 'Bahan Baku' yang dapat digunakan untuk pemakaian bahan baku.");
                }

                $quantity = (float) ($validated['barang_qty'][$index] ?? 0);

                // Validate quantity
                if ($quantity <= 0) {
                    throw new \Exception('Quantity harus lebih dari 0');
                }

                // Validate stock
                if ($barang->stok < $quantity) {
                    throw new \Exception("Stok {$barang->nama_barang} tidak mencukupi. Stok tersedia: {$barang->stok}, dibutuhkan: {$quantity}");
                }
                
                // Use harga_beli from database, not from form (to ensure consistency with saldo awal)
                $hargaBeli = $barang->harga_beli;
                if ($hargaBeli <= 0) {
                    throw new \Exception("Barang {$barang->nama_barang} tidak memiliki harga beli. Pastikan barang sudah ada di saldo awal.");
                }
                
                // Consume Inventory (FIFO/LIFO/AVG) to get Actual Cost
                $costData = $inventoryService->consumeInventory($barang, $quantity);

                // Use Actual Cost for 'harga' and 'jumlah' in transaction record
                // (or keep 'harga' as standard cost and use 'hpp' columns?
                // Prompt: "Transaction output must store the actual cost used".
                // Current table structure has 'harga' and 'jumlah'.
                // If I overwrite 'harga' with actual unit cost, it might vary per transaction.
                // Standard practice: Usage transaction value IS the actual cost.
                // So I will use the calculated cost.

                $unitCost = $costData['unit_cost'];
                $totalCost = $costData['total_cost'];

                // Create detail - gunakan data dari master
                $pemakaian->details()->create([
                    'barang_id' => $barang->id,
                    'barang_nama' => $barang->nama_barang,
                    'barang_kode' => $barang->kode_barang,
                    'quantity' => $quantity,
                    'satuan' => $barang->satuan,
                    'harga' => $unitCost, // Actual Cost
                    'jumlah' => $totalCost, // Actual Total Cost
                    'hpp_unit' => $unitCost,
                    'hpp_total' => $totalCost,
                ]);

                // Reduce stock: stok_akhir = stok_awal - keluar
                $barang->reduceStock($quantity);
                
                // Ensure stock doesn't go negative
                $barang->refresh();
                if ($barang->stok < 0) {
                    $barang->increment('stok', $quantity);
                    throw new \Exception("Stok {$barang->nama_barang} tidak boleh negatif");
                }

                $totalHarga += $jumlah;
            }

            // Update total
            $pemakaian->update(['total_harga' => $totalHarga]);

            DB::commit();

            return redirect()->route('output-barang.pemakaian-bahan-baku')
                ->with('success', 'Data pemakaian bahan baku berhasil disimpan! Total: Rp ' . number_format($totalHarga, 0, ',', '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show form Pemakaian Barang Dalam Proses
     */
    public function pemakaianBarangDalamProses()
    {
        $barangs = Barang::all();
        return view('output-barang.pemakaian-barang-dalam-proses', compact('barangs'));
    }

    /**
     * Store Pemakaian Barang Dalam Proses
     */
    public function storePemakaianBarangDalamProses(PemakaianBarangDalamProsesRequest $request, InventoryService $inventoryService)
    {
        // Semua request sudah tervalidasi otomatis oleh PemakaianBarangDalamProsesRequest
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Validate stock availability first
            $stockErrors = [];
            foreach ($validated['barang_kode'] as $index => $barangKode) {
                $quantity = (float) ($validated['barang_qty'][$index] ?? 0);
                
                // Ambil barang dari master
                $barang = Barang::where('kode_barang', $barangKode)->first();
                
                if (!$barang) {
                    $stockErrors[] = "Barang dengan kode {$barangKode} tidak ditemukan";
                    continue;
                }
                
                // Validasi kategori: hanya "Barang Dalam Proses" yang diperbolehkan
                $kategori = strtolower(trim($barang->deskripsi ?? ''));
                $allowedKategori = ['barang dalam proses', 'barang_dalam_proses', 'barangdalamproses', 'barang_proses', 'barangproses'];
                
                // Normalize check
                $normalizedKategori = str_replace('_', ' ', $kategori);
                $normalizedAllowed = array_map(function($k) {
                    return str_replace('_', ' ', strtolower($k));
                }, $allowedKategori);

                if (!in_array($normalizedKategori, $normalizedAllowed) && !in_array($kategori, $allowedKategori)) {
                    $stockErrors[] = "Barang '{$barang->nama_barang}' bukan kategori Barang Dalam Proses. Hanya barang dengan kategori 'Barang Dalam Proses' yang dapat digunakan untuk pemakaian barang dalam proses.";
                    continue;
                }

                if ($barang->stok < $quantity) {
                    $stockErrors[] = "Stok {$barang->nama_barang} tidak mencukupi. Stok tersedia: {$barang->stok}, dibutuhkan: {$quantity}";
                }
            }

            if (!empty($stockErrors)) {
                throw new \Exception(implode(', ', $stockErrors));
            }

            // Ambil customer dari master data berdasarkan nama
            $customer = Customer::where('nama_customer', $validated['nama_customer'])->first();
            
            if (!$customer) {
                return back()->withErrors(['nama_customer' => 'Customer tidak ditemukan.'])->withInput();
            }

            // Pastikan kode customer otomatis benar
            $kodeCustomer = $customer->kode_customer;

            // Create header
            $pemakaian = PemakaianBarangDalamProses::create([
                'tanggal' => $validated['tanggal'],
                'nomor_bukti' => $validated['nomor_bukti'],
                'keterangan' => $validated['keterangan'] ?? '',
                'nama_customer' => $customer->nama_customer, // Nama Departemen/Produksi - dari master customer
                'kode_customer' => $kodeCustomer, // Kode Referensi - auto dari master customer
                'total_harga' => 0,
            ]);

            // Create details and reduce stock
            $totalHarga = 0;
            foreach ($validated['barang_kode'] as $index => $barangKode) {
                // Ambil barang dari master berdasarkan kode
                $barang = Barang::where('kode_barang', $barangKode)->first();
                if (!$barang) {
                    return back()->withErrors([
                        'barang_kode.' . $index => 'Barang dengan kode "' . $barangKode . '" tidak ditemukan di master.'
                    ])->withInput();
                }
                
                // Validasi kategori: hanya "Barang Dalam Proses" yang diperbolehkan
                $kategori = strtolower(trim($barang->deskripsi ?? ''));
                $allowedKategori = ['barang dalam proses', 'barang_dalam_proses', 'barangdalamproses', 'barang_proses', 'barangproses'];
                
                if (!in_array($kategori, $allowedKategori)) {
                    throw new \Exception("Barang '{$barang->nama_barang}' bukan kategori Barang Dalam Proses. Hanya barang dengan kategori 'Barang Dalam Proses' yang dapat digunakan untuk pemakaian barang dalam proses.");
                }

                $quantity = (float) ($validated['barang_qty'][$index] ?? 0);

                // Validate quantity
                if ($quantity <= 0) {
                    throw new \Exception('Quantity harus lebih dari 0');
                }

                // Validate stock
                if ($barang->stok < $quantity) {
                    throw new \Exception("Stok {$barang->nama_barang} tidak mencukupi. Stok tersedia: {$barang->stok}, dibutuhkan: {$quantity}");
                }
                
                // Use harga_beli from database, not from form (to ensure consistency with saldo awal)
                $hargaBeli = $barang->harga_beli;
                if ($hargaBeli <= 0) {
                    throw new \Exception("Barang {$barang->nama_barang} tidak memiliki harga beli. Pastikan barang sudah ada di saldo awal.");
                }
                
                // Consume Inventory (FIFO/LIFO/AVG) to get Actual Cost
                $costData = $inventoryService->consumeInventory($barang, $quantity);

                $unitCost = $costData['unit_cost'];
                $totalCost = $costData['total_cost'];

                // Create detail - gunakan data dari master
                $pemakaian->details()->create([
                    'barang_id' => $barang->id,
                    'barang_nama' => $barang->nama_barang,
                    'barang_kode' => $barang->kode_barang,
                    'quantity' => $quantity,
                    'satuan' => $barang->satuan,
                    'harga' => $unitCost, // Actual Cost
                    'jumlah' => $totalCost, // Actual Total Cost
                    'hpp_unit' => $unitCost,
                    'hpp_total' => $totalCost,
                ]);

                // Reduce stock: stok_akhir = stok_awal - keluar
                $barang->reduceStock($quantity);
                
                // Ensure stock doesn't go negative
                $barang->refresh();
                if ($barang->stok < 0) {
                    $barang->increment('stok', $quantity);
                    throw new \Exception("Stok {$barang->nama_barang} tidak boleh negatif");
                }

                $totalHarga += $jumlah;
            }

            // Update total
            $pemakaian->update(['total_harga' => $totalHarga]);

            DB::commit();

            return redirect()->route('output-barang.pemakaian-barang-dalam-proses')
                ->with('success', 'Data pemakaian barang dalam proses berhasil disimpan! Total: Rp ' . number_format($totalHarga, 0, ',', '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
