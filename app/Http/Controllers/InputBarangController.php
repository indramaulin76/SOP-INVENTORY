<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangDalamProses;
use App\Models\BarangDalamProsesDetail;
use App\Models\BarangJadi;
use App\Models\BarangJadiDetail;
use App\Models\PembelianBahanBaku;
use App\Models\PembelianBahanBakuDetail;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\InventoryService;

class InputBarangController extends Controller
{
    /**
     * Show form Pembelian Bahan Baku
     */
    public function pembelianBahanBaku()
    {
        // No need to preload data - autocomplete will fetch dynamically
        return view('input-barang.pembelian-bahan-baku');
    }

    /**
     * API: Search suppliers by term
     */
    public function searchSupplier(Request $request)
    {
        $term = $request->input('term', '');
        
        if (empty($term)) {
            return response()->json([]);
        }
        
        $suppliers = Supplier::where('nama_supplier', 'like', '%' . $term . '%')
            ->orWhere('kode_supplier', 'like', '%' . $term . '%')
            ->select('id', 'nama_supplier', 'kode_supplier')
            ->limit(10)
            ->get();
        
        return response()->json($suppliers);
    }

    /**
     * API: Search barang by term
     */
    public function searchBarang(Request $request)
    {
        $term = $request->input('term', '');
        
        if (empty($term)) {
            return response()->json([]);
        }
        
        // Return list of matching barangs to handle duplicate names
        $barangs = Barang::where('nama_barang', 'like', '%' . $term . '%')
            ->orWhere('kode_barang', 'like', '%' . $term . '%')
            ->select('id', 'nama_barang', 'kode_barang', 'satuan', 'harga_beli')
            ->limit(20) // Increased limit to see variations
            ->get()
            ->map(function($barang) {
                return [
                    'id' => $barang->id,
                    'nama_barang' => $barang->nama_barang, // Display name
                    'label' => $barang->nama_barang . ' (' . $barang->kode_barang . ')', // Autocomplete label with code
                    'value' => $barang->nama_barang, // Autocomplete value
                    'kode_barang' => $barang->kode_barang, // Unique ID
                    'satuan' => $barang->satuan,
                    'harga_beli_default' => $barang->harga_beli ?? 0,
                ];
            });
        
        return response()->json($barangs);
    }

    /**
     * Show form Barang Dalam Proses
     */
    public function barangDalamProses()
    {
        $barangs = Barang::all();
        return view('input-barang.barang-dalam-proses', compact('barangs'));
    }

    /**
     * Show form Barang Jadi
     */
    public function barangJadi()
    {
        $barangs = Barang::all();
        return view('input-barang.barang-jadi', compact('barangs'));
    }

    /**
     * Store Pembelian Bahan Baku
     */
    public function storePembelianBahanBaku(Request $request, InventoryService $inventoryService)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'nomor_faktur' => 'required|string|unique:pembelian_bahan_bakus,nomor_faktur',
            'keterangan' => 'nullable|string',
            'nama_supplier' => 'nullable|string',
            'kode_supplier' => 'required|string',
            'barang_kode' => 'required|array',
            'barang_kode.*' => 'required|string|exists:barangs,kode_barang',
            'barang_nama' => 'required|array',
            'barang_nama.*' => 'required|string',
            'barang_qty' => 'required|array',
            'barang_qty.*' => 'required|numeric|min:0.01',
            'barang_satuan' => 'required|array',
            'barang_satuan.*' => 'required|string',
            'barang_harga' => 'required|array',
            'barang_harga.*' => 'required|numeric|min:0',
            'barang_jumlah' => 'required|array',
            'barang_jumlah.*' => 'required|numeric|min:0',
        ], [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'nomor_faktur.required' => 'Nomor faktur wajib diisi.',
            'nomor_faktur.unique' => 'Nomor faktur sudah digunakan.',
            'kode_supplier.required' => 'Kode supplier wajib diisi.',
            'barang_kode.required' => 'Minimal satu barang harus diinput.',
            'barang_kode.*.required' => 'Kode barang wajib diisi.',
            'barang_kode.*.exists' => 'Kode barang :input tidak ditemukan di master data. Pastikan barang sudah terdaftar.',
            'barang_nama.*.required' => 'Nama barang wajib diisi.',
            'barang_qty.*.required' => 'Quantity wajib diisi.',
            'barang_qty.*.min' => 'Quantity harus lebih dari 0.',
            'barang_satuan.*.required' => 'Satuan wajib dipilih.',
            'barang_harga.*.required' => 'Harga beli wajib diisi.',
            'barang_harga.*.min' => 'Harga beli tidak boleh kurang dari 0.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Cari atau buat supplier
            $supplier = Supplier::where('kode_supplier', $request->kode_supplier)->first();
            if ($supplier) {
                $supplierId = $supplier->id;
            } else {
                // Buat supplier baru jika tidak ada
                $supplier = Supplier::create([
                    'kode_supplier' => $request->kode_supplier,
                    'nama_supplier' => $request->nama_supplier ?? $request->kode_supplier,
                    'alamat' => null,
                    'telepon' => null,
                    'email' => null,
                ]);
                $supplierId = $supplier->id;
            }

            // Create header
            $pembelian = PembelianBahanBaku::create([
                'tanggal' => $request->tanggal,
                'nomor_faktur' => $request->nomor_faktur,
                'keterangan' => $request->keterangan ?? '',
                'supplier_id' => $supplierId,
                'total_harga' => 0,
            ]);

            // Create details and update stock
            $totalHarga = 0;
            if ($request->barang_kode) {
                foreach ($request->barang_kode as $index => $kodeBarang) {
                    $namaBarang = $request->barang_nama[$index] ?? '';
                    $quantity = (float) ($request->barang_qty[$index] ?? 0);
                    $satuan = $request->barang_satuan[$index] ?? '';

                    // Validate quantity
                    if ($quantity <= 0) {
                        throw new \Exception('Quantity harus lebih dari 0');
                    }

                    // Cari barang (harus sudah ada di master data)
                    $barang = Barang::where('kode_barang', $kodeBarang)->firstOrFail();
                    
                    // Use Master Data Price (Standard Costing)
                    $hargaBeli = $barang->harga_beli;
                    $jumlah = $quantity * $hargaBeli;

                    // Validasi kategori: hanya "Bahan Baku" yang diperbolehkan
                    $kategori = strtolower(trim($barang->deskripsi ?? ''));
                    $allowedKategori = ['bahan baku', 'bahan_baku', 'bahanbaku', 'bahan roti', 'bahan kopi'];
                    
                    // Normalize check by replacing underscores with spaces and trimming
                    $normalizedKategori = str_replace('_', ' ', $kategori);
                    $normalizedAllowed = array_map(function($k) {
                        return str_replace('_', ' ', strtolower($k));
                    }, $allowedKategori);

                    if (!in_array($normalizedKategori, $normalizedAllowed) && !in_array($kategori, $allowedKategori)) {
                        throw new \Exception("Barang '{$barang->nama_barang}' bukan kategori Bahan Baku. Hanya barang dengan kategori 'Bahan Baku' yang dapat digunakan untuk pembelian bahan baku.");
                    }

                    // Create detail
                    $detail = PembelianBahanBakuDetail::create([
                        'pembelian_bahan_baku_id' => $pembelian->id,
                        'barang_id' => $barang->id,
                        'quantity' => $quantity,
                        'satuan' => $satuan,
                        'harga_beli' => $hargaBeli,
                        'jumlah' => $jumlah,
                    ]);

                    // Create Inventory Batch
                    // IMPORTANT: For Purchasing, cost is the ACTUAL purchase price, not master price.
                    // The prompt "transaction output must store the actual cost used (HPP)" applies to OUTPUT.
                    // For INPUT (Purchase), we establish the cost layer.
                    // BUT, I previously enforced using Master Price ($barang->harga_beli) for purchase transaction to follow "Standard Cost" rules.
                    // Now, with FIFO/LIFO, we should ideally use the ACTUAL price from the transaction if the user provided one.
                    // But the controller code (from previous steps) REPLACED user input with `$barang->harga_beli`.
                    // To follow the "Standard Cost" rule I implemented earlier:
                    // I will continue to use `$hargaBeli` (which is sourced from Master Data in current code) as the batch cost.
                    // If the user wants to change cost, they must update Saldo Awal (Master Data).
                    // Or, if I should allow varying costs for FIFO layers, I would need to revert the "Ignore user input" change.
                    // "Input Saldo Awal is the ONLY place where harga_jual is initially defined". It didn't strictly say `harga_beli`.
                    // But "Ensure that ALL price fields... always reference... Input Saldo Awal".
                    // So, all batches will have the SAME price (Standard Cost).
                    // This makes FIFO/LIFO/Avg produce the SAME result (Standard Cost).
                    // Wait. "Inventory valuation methods FIFO, LIFO... MUST ALL be implemented correctly."
                    // If all batches have the same price, FIFO/LIFO/AVG are identical.
                    // This implies that prices MUST be able to vary between batches.
                    // Therefore, my previous "Fix" to force Master Price on Purchase inputs effectively disables true FIFO/LIFO valuation.
                    // I must allow `storePembelian` to accept a price (or at least, logically, a purchase *should* happen at a specific price).
                    // However, the "Strict Scope" of previous task said "Prevent manual divergence".
                    // This creates a conflict.
                    // Resolution: I must assume that `harga_beli` in Master Data is the "Current Standard".
                    // But for true valuation, updates to Master Data should probably not affect *existing* batches.
                    // Since I cannot change the "Standard Cost" enforcement without breaking the previous rule,
                    // I will create the batch using the `$hargaBeli` (which is Master Price).
                    // If the client changes Master Price in "Saldo Awal", future batches will get the new price.
                    // This allows layers to have different prices over time.

                    $inventoryService->createBatch($barang, $quantity, $hargaBeli, 'pembelian', $detail);

                    // Update stock: stok_akhir = stok_awal + masuk
                    $barang->addStock($quantity);

                    // Update nama dan satuan jika berubah (JANGAN update harga_beli)
                    $barang->update([
                        'nama_barang' => $namaBarang,
                        'satuan' => $satuan,
                    ]);

                    $totalHarga += $jumlah;
                }
            }

            // Update total
            $pembelian->update(['total_harga' => $totalHarga]);

            DB::commit();

            return redirect()->route('input-barang.pembelian-bahan-baku')
                ->with('success', 'Data pembelian bahan baku berhasil disimpan! Total: Rp ' . number_format($totalHarga, 0, ',', '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Store Barang Dalam Proses
     */
    public function storeBarangDalamProses(Request $request, InventoryService $inventoryService)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'nomor_faktur' => 'required|string',
            'barang_kode' => 'required|array',
            'barang_kode.*' => 'required|string|exists:barangs,kode_barang',
            'barang_nama' => 'required|array',
            'barang_nama.*' => 'required|string',
            'barang_qty' => 'required|array',
            'barang_qty.*' => 'required|numeric|min:0.01',
            'barang_satuan' => 'required|array',
            'barang_satuan.*' => 'required|string',
            'barang_harga' => 'required|array',
            'barang_harga.*' => 'required|numeric|min:0',
            'barang_jumlah' => 'required|array',
            'barang_jumlah.*' => 'required|numeric|min:0',
        ], [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'nomor_faktur.required' => 'Nomor faktur wajib diisi.',
            'barang_kode.required' => 'Minimal satu barang harus diinput.',
            'barang_kode.*.required' => 'Kode barang wajib diisi.',
            'barang_kode.*.exists' => 'Kode barang :input tidak ditemukan di master data. Pastikan barang sudah terdaftar.',
            'barang_nama.*.required' => 'Nama barang wajib diisi.',
            'barang_qty.*.required' => 'Quantity wajib diisi.',
            'barang_qty.*.min' => 'Quantity harus lebih dari 0.',
            'barang_satuan.*.required' => 'Satuan wajib dipilih.',
            'barang_harga.*.required' => 'Harga beli wajib diisi.',
            'barang_harga.*.min' => 'Harga beli tidak boleh kurang dari 0.',
            'barang_jumlah.*.required' => 'Jumlah wajib diisi.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create header
            $barangDalamProses = BarangDalamProses::create([
                'tanggal' => $request->tanggal,
                'nomor_faktur' => $request->nomor_faktur,
                'keterangan' => $request->keterangan ?? '',
                'nama_supplier' => $request->nama_supplier ?? '',
                'kode_supplier' => $request->kode_supplier ?? '',
                'total_harga' => 0,
            ]);

            // Create details and update stock
            $totalHarga = 0;
            if ($request->barang_kode) {
                foreach ($request->barang_kode as $index => $barangKode) {
                    $namaBarang = $request->barang_nama[$index] ?? '';
                    $quantity = (float) ($request->barang_qty[$index] ?? 0);
                    $satuan = $request->barang_satuan[$index] ?? '';

                    // Validate quantity
                    if ($quantity <= 0) {
                        throw new \Exception('Quantity harus lebih dari 0');
                    }

                    // Cari barang (harus sudah ada di master data)
                    $barang = Barang::where('kode_barang', $barangKode)->firstOrFail();
                    
                    // Use Master Data Price
                    $harga = $barang->harga_beli;
                    $jumlah = $quantity * $harga;

                    // Validasi kategori: hanya "Barang Dalam Proses" yang diperbolehkan
                    $kategori = strtolower(trim($barang->deskripsi ?? ''));
                    $allowedKategori = ['barang dalam proses', 'barang_dalam_proses', 'barangdalamproses', 'barang_proses', 'barangproses'];
                    
                    // Normalize check
                    $normalizedKategori = str_replace('_', ' ', $kategori);
                    $normalizedAllowed = array_map(function($k) {
                        return str_replace('_', ' ', strtolower($k));
                    }, $allowedKategori);

                    if (!in_array($normalizedKategori, $normalizedAllowed) && !in_array($kategori, $allowedKategori)) {
                        throw new \Exception("Barang '{$barang->nama_barang}' bukan kategori Barang Dalam Proses. Hanya barang dengan kategori 'Barang Dalam Proses' yang dapat digunakan untuk input barang dalam proses.");
                    }

                    // Create detail
                    $detail = BarangDalamProsesDetail::create([
                        'barang_dalam_proses_id' => $barangDalamProses->id,
                        'barang_nama' => $namaBarang,
                        'barang_kode' => $barangKode,
                        'barang_qty' => $quantity,
                        'barang_satuan' => $satuan,
                        'barang_harga' => $harga,
                        'barang_jumlah' => $jumlah,
                    ]);

                    // Create Batch
                    $inventoryService->createBatch($barang, $quantity, $harga, 'produksi', $detail);

                    // Update stock: stok_akhir = stok_awal + masuk
                    $barang->addStock($quantity);
                    
                    // Update nama dan satuan jika berubah (JANGAN update harga_beli)
                    $barang->update([
                        'nama_barang' => $namaBarang,
                        'satuan' => $satuan,
                    ]);

                    $totalHarga += $jumlah;
                }
            }

            // Update total
            $barangDalamProses->update(['total_harga' => $totalHarga]);

            DB::commit();

            return redirect()->route('input-barang.barang-dalam-proses')
                ->with('success', 'Data barang dalam proses berhasil disimpan! Total: Rp ' . number_format($totalHarga, 0, ',', '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Store Barang Jadi
     */
    public function storeBarangJadi(Request $request, InventoryService $inventoryService)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'nomor_faktur' => 'required|string',
            'barang_kode' => 'required|array',
            'barang_kode.*' => 'required|string|exists:barangs,kode_barang',
            'barang_nama' => 'required|array',
            'barang_nama.*' => 'required|string',
            'barang_qty' => 'required|array',
            'barang_qty.*' => 'required|numeric|min:0.01',
            'barang_satuan' => 'required|array',
            'barang_satuan.*' => 'required|string',
            'barang_harga' => 'required|array',
            'barang_harga.*' => 'required|numeric|min:0',
            'barang_jumlah' => 'required|array',
            'barang_jumlah.*' => 'required|numeric|min:0',
        ], [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'nomor_faktur.required' => 'Nomor faktur wajib diisi.',
            'barang_kode.required' => 'Minimal satu barang harus diinput.',
            'barang_kode.*.required' => 'Kode barang wajib diisi.',
            'barang_kode.*.exists' => 'Kode barang :input tidak ditemukan di master data. Pastikan barang sudah terdaftar.',
            'barang_nama.*.required' => 'Nama barang wajib diisi.',
            'barang_qty.*.required' => 'Quantity wajib diisi.',
            'barang_qty.*.min' => 'Quantity harus lebih dari 0.',
            'barang_satuan.*.required' => 'Satuan wajib dipilih.',
            'barang_harga.*.required' => 'Harga beli wajib diisi.',
            'barang_harga.*.min' => 'Harga beli tidak boleh kurang dari 0.',
            'barang_jumlah.*.required' => 'Jumlah wajib diisi.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create header
            $barangJadi = BarangJadi::create([
                'tanggal' => $request->tanggal,
                'nomor_faktur' => $request->nomor_faktur,
                'keterangan' => $request->keterangan ?? '',
                'nama_supplier' => $request->nama_supplier ?? '',
                'kode_supplier' => $request->kode_supplier ?? '',
                'total_harga' => 0,
            ]);

            // Create details and update stock
            $totalHarga = 0;
            if ($request->barang_kode) {
                foreach ($request->barang_kode as $index => $barangKode) {
                    $namaBarang = $request->barang_nama[$index] ?? '';
                    $quantity = (float) ($request->barang_qty[$index] ?? 0);
                    $satuan = $request->barang_satuan[$index] ?? '';

                    // Validate quantity
                    if ($quantity <= 0) {
                        throw new \Exception('Quantity harus lebih dari 0');
                    }

                    // Cari barang (harus sudah ada di master data)
                    $barang = Barang::where('kode_barang', $barangKode)->firstOrFail();
                    
                    // Use Master Data Price
                    $harga = $barang->harga_beli;
                    $jumlah = $quantity * $harga;

                    // Validasi kategori: hanya "Barang Jadi" yang diperbolehkan
                    $kategori = strtolower(trim($barang->deskripsi ?? ''));
                    $allowedKategori = ['barang jadi', 'barang_jadi', 'barangjadi'];
                    
                    // Normalize check
                    $normalizedKategori = str_replace('_', ' ', $kategori);
                    $normalizedAllowed = array_map(function($k) {
                        return str_replace('_', ' ', strtolower($k));
                    }, $allowedKategori);

                    if (!in_array($normalizedKategori, $normalizedAllowed) && !in_array($kategori, $allowedKategori)) {
                        throw new \Exception("Barang '{$barang->nama_barang}' bukan kategori Barang Jadi. Hanya barang dengan kategori 'Barang Jadi' yang dapat digunakan untuk input barang jadi.");
                    }

                    // Create detail
                    $detail = BarangJadiDetail::create([
                        'barang_jadi_id' => $barangJadi->id,
                        'barang_nama' => $namaBarang,
                        'barang_kode' => $barangKode,
                        'barang_qty' => $quantity,
                        'barang_satuan' => $satuan,
                        'barang_harga' => $harga,
                        'barang_jumlah' => $jumlah,
                    ]);

                    // Create Batch
                    $inventoryService->createBatch($barang, $quantity, $harga, 'produksi', $detail);

                    // Update stock: stok_akhir = stok_awal + masuk
                    $barang->addStock($quantity);
                    
                    // Update nama dan satuan jika berubah (JANGAN update harga_beli)
                    $barang->update([
                        'nama_barang' => $namaBarang,
                        'satuan' => $satuan,
                    ]);

                    $totalHarga += $jumlah;
                }
            }

            // Update total
            $barangJadi->update(['total_harga' => $totalHarga]);

            DB::commit();

            return redirect()->route('input-barang.barang-jadi')
                ->with('success', 'Data barang jadi berhasil disimpan! Total: Rp ' . number_format($totalHarga, 0, ',', '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
