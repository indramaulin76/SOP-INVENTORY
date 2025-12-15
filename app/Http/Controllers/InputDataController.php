<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Supplier;
use App\Models\Customer;
use App\Http\Requests\SaldoAwalRequest;
use App\Http\Requests\CustomerRequest;
use App\Http\Requests\SupplierRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\InventoryService;

class InputDataController extends Controller
{
    /**
     * Show Input Data Barang form
     */
    public function barang()
    {
        return view('input-data.barang');
    }

    /**
     * Show Input Data Supplier form
     */
    public function supplier()
    {
        // Generate kode supplier otomatis untuk preview
        $last = Supplier::orderBy('id', 'DESC')->first();
        if (!$last) {
            $newCode = 'SUP001';
        } else {
            // Extract number from last kode_supplier
            $lastCode = $last->kode_supplier;
            if (preg_match('/SUP(\d+)/', $lastCode, $matches)) {
                $num = (int) $matches[1];
                $num++;
                $newCode = 'SUP' . str_pad($num, 3, '0', STR_PAD_LEFT);
            } else {
                // If format doesn't match, start from 001
                $newCode = 'SUP001';
            }
        }
        
        return view('input-data.supplier', compact('newCode'));
    }

    /**
     * Show Input Data Customer form
     */
    public function customer()
    {
        // Generate kode customer otomatis untuk preview
        $last = Customer::orderBy('id', 'DESC')->first();
        if (!$last) {
            $kode = 'CUS001';
        } else {
            // Extract number from last kode_customer
            $lastCode = $last->kode_customer;
            if (preg_match('/CUS(\d+)/', $lastCode, $matches)) {
                $num = (int) $matches[1];
                $num++;
                $kode = 'CUS' . str_pad($num, 3, '0', STR_PAD_LEFT);
            } else {
                // If format doesn't match, start from 001
                $kode = 'CUS001';
            }
        }
        
        return view('input-data.customer', compact('kode'));
    }

    /**
     * Show Input Data Saldo Awal form
     */
    public function saldo()
    {
        // Get all barang with saldo awal (stok > 0 AND harga_beli > 0)
        $barangs = Barang::where('stok', '>', 0)
            ->where('harga_beli', '>', 0)
            ->orderBy('kode_barang')
            ->get();
        
        // Calculate total saldo awal
        $totalSaldo = $barangs->sum(function($barang) {
            return $barang->stok * $barang->harga_beli;
        });
        
        return view('input-data.saldo', compact('barangs', 'totalSaldo'));
    }

    /**
     * Store barang data
     */
    public function storeBarang(Request $request)
    {
        // Validation outside transaction to allow normal redirects
        $validated = $request->validate([
            'nama_barang' => 'required|string',
            'kategori' => 'required|string',
            'limit_stock' => 'required|integer|min:0',
            'satuan' => 'required|string',
        ], [
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'kategori.required' => 'Kategori barang wajib dipilih.',
            'limit_stock.required' => 'Limit stock wajib diisi.',
            'limit_stock.integer' => 'Limit stock harus berupa angka bulat.',
            'limit_stock.min' => 'Limit stock tidak boleh kurang dari 0.',
            'satuan.required' => 'Jenis satuan wajib dipilih.',
        ]);

        try {
            DB::beginTransaction();

            // AUTO GENERATE KODE BARANG
            // Lock the table to prevent race conditions during code generation
            // Note: pessimistic locking on the last record is a simple way to serialize access
            $last = Barang::lockForUpdate()->orderBy('id', 'DESC')->first();

            if (!$last) {
                $newCode = 'KODE-001';
            } else {
                // Extract number from last kode_barang
                $lastCode = $last->kode_barang;
                if (preg_match('/KODE-(\d+)/', $lastCode, $matches)) {
                    $num = (int) $matches[1];
                    $num++;
                    $newCode = 'KODE-' . str_pad($num, 3, '0', STR_PAD_LEFT);
                } else {
                    // If format doesn't match, start from 001
                    $newCode = 'KODE-001';
                }
            }

            // Use validated data or generated code
            Barang::create([
                'kode_barang' => $newCode,
                'nama_barang' => $validated['nama_barang'],
                'deskripsi' => $validated['kategori'], // Simpan kategori di field deskripsi untuk sekarang
                'satuan' => $validated['satuan'],
                'stok' => (int) $validated['limit_stock'], // Pastikan integer
                'harga_beli' => 0,
                'harga_jual' => 0,
            ]);

            DB::commit();

            return redirect()->route('input-data.barang')->with('success', 'Data barang berhasil disimpan! Kode barang: ' . $newCode);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Store supplier data
     */
    public function storeSupplier(SupplierRequest $request)
    {
        // Semua request sudah tervalidasi otomatis oleh SupplierRequest
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Generate kode supplier otomatis
            $last = Supplier::lockForUpdate()->orderBy('id', 'DESC')->first();
            if (!$last) {
                $newCode = 'SUP001';
            } else {
                // Extract number from last kode_supplier
                $lastCode = $last->kode_supplier;
                if (preg_match('/SUP(\d+)/', $lastCode, $matches)) {
                    $num = (int) $matches[1];
                    $num++;
                    $newCode = 'SUP' . str_pad($num, 3, '0', STR_PAD_LEFT);
                } else {
                    // If format doesn't match, start from 001
                    $newCode = 'SUP001';
                }
            }

            Supplier::create([
                'kode_supplier' => $newCode,
                'nama_supplier' => $validated['nama_supplier'],
                'alamat' => $validated['alamat'],
                'telepon' => $validated['telepon'],
                'email' => $validated['email'] ?? null,
                'nama_pemilik' => $validated['nama_pemilik'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            DB::commit();

            return redirect()->route('input-data.supplier')->with('success', 'Data supplier berhasil disimpan! Kode supplier: ' . $newCode);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Store customer data
     */
    public function storeCustomer(CustomerRequest $request)
    {
        // Semua request sudah tervalidasi otomatis oleh CustomerRequest
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Generate kode customer otomatis
            $last = Customer::lockForUpdate()->orderBy('id', 'DESC')->first();
            if (!$last) {
                $newCode = 'CUS001';
            } else {
                // Extract number from last kode_customer
                $lastCode = $last->kode_customer;
                if (preg_match('/CUS(\d+)/', $lastCode, $matches)) {
                    $num = (int) $matches[1];
                    $num++;
                    $newCode = 'CUS' . str_pad($num, 3, '0', STR_PAD_LEFT);
                } else {
                    // If format doesn't match, start from 001
                    $newCode = 'CUS001';
                }
            }

            Customer::create([
                'kode_customer' => $newCode,
                'nama_customer' => $validated['nama_customer'],
                'alamat' => $validated['alamat'],
                'telepon' => $validated['telepon'],
                'email' => $validated['email'] ?? null,
                'tipe_customer' => $validated['tipe_customer'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            DB::commit();

            return redirect()->route('input-data.customer')->with('success', 'Data customer berhasil disimpan! Kode customer: ' . $newCode);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Store saldo awal data
     */
    public function storeSaldo(SaldoAwalRequest $request, InventoryService $inventoryService)
    {
        // Semua request sudah tervalidasi otomatis oleh SaldoAwalRequest
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $totalSaldo = 0;
            $submittedKodes = []; // Simpan kode barang yang ada di form
            
            foreach ($validated['barang_kode'] as $index => $kodeBarang) {
                $namaBarang = $validated['barang_nama'][$index];
                $kategori = $validated['barang_kategori'][$index];
                $qty = (float) $validated['barang_qty'][$index];
                $satuan = $validated['barang_satuan'][$index];
                $hargaBeli = (float) $validated['barang_harga'][$index];
                $hargaJual = (float) $validated['barang_harga_jual'][$index];
                $jumlah = (float) $validated['barang_jumlah'][$index];
                
                $submittedKodes[] = $kodeBarang;
                
                // Barang harus sudah ada di master data (divalidasi oleh FormRequest)
                $barang = Barang::where('kode_barang', $kodeBarang)->lockForUpdate()->first();

                if (!$barang) {
                    throw new \Exception("Barang dengan kode {$kodeBarang} tidak ditemukan.");
                }

                // Reset stock first if it's a "Saldo Awal" overwrite logic (assumption: Saldo Awal is initial state)
                // But per instructions "Input Saldo Awal is considered the FIRST inventory batch".
                // If the user *updates* saldo awal, we should strictly speaking adjust batches.
                // Given the constraints and "No legacy data", we assume this runs initially.
                // However, the code below used to just update `stok`.
                // I need to create a batch for this qty.
                // Important: If stok > 0 already, this might duplicate if run again.
                // "Input Saldo Awal" usually implies setting the *starting* point.
                // I will add the difference or just set it?
                // The previous code did `$barang->update(['stok' => $qty])`.
                // So it forces the stock to be $qty.
                // To maintain "Batch" integrity, I should clear previous "saldo_awal" batches for this item and create a new one.

                // Remove old saldo_awal batches to avoid duplication on re-save
                \App\Models\InventoryBatch::where('barang_id', $barang->id)
                    ->where('sumber', 'saldo_awal')
                    ->delete();

                // Create new batch
                if ($qty > 0) {
                    $inventoryService->createBatch($barang, $qty, $hargaBeli, 'saldo_awal');
                }

                // Update barang master data
                // Note: stok will be updated by createBatch logic? No, createBatch just creates the record.
                // I need to update the master stock too?
                // Wait, in standard systems, master stock is sum of batches.
                // Previous code: $barang->update(['stok' => $qty]).
                // I should keep master stock in sync.
                // Since this is "Input Saldo", we are defining the TOTAL stock.
                
                $barang->update([
                    'deskripsi' => $kategori,
                    'satuan' => $satuan,
                    'stok' => $qty, // Force set stock to match input
                    'harga_beli' => $hargaBeli,
                    'harga_jual' => $hargaJual,
                ]);
                
                $totalSaldo += $jumlah;
            }
            
            // Hapus saldo awal untuk barang yang tidak ada di form (reset to 0)
            $itemsReset = Barang::where('stok', '>', 0)
                ->where('harga_beli', '>', 0)
                ->whereNotIn('kode_barang', $submittedKodes)
                ->get();

            foreach ($itemsReset as $item) {
                // Clear batches
                \App\Models\InventoryBatch::where('barang_id', $item->id)
                    ->where('sumber', 'saldo_awal')
                    ->delete();

                $item->update([
                    'stok' => 0,
                    'harga_beli' => 0,
                ]);
            }

            DB::commit();

            return redirect()->route('input-data.saldo')
                ->with('success', 'Data saldo awal berhasil disimpan! Total saldo: Rp ' . number_format($totalSaldo, 0, ',', '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('input-data.saldo')
                ->with('error', 'Gagal menyimpan data saldo awal: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete barang data
     */
    public function destroyBarang($id)
    {
        // Check if user has permission (admin or superadmin)
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Unauthorized access. Only admin and pimpinan can delete barang.');
        }

        try {
            $barang = Barang::findOrFail($id);
            $barang->delete();

            return redirect()->route('report.laporan-data-barang')
                ->with('success', 'Data barang berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('report.laporan-data-barang')
                ->with('error', 'Gagal menghapus data barang: ' . $e->getMessage());
        }
    }

    /**
     * Delete supplier data
     */
    public function destroySupplier($id)
    {
        // Check if user has permission (admin or superadmin)
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Unauthorized access. Only admin and pimpinan can delete supplier.');
        }

        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->delete();

            return redirect()->route('report.laporan-data-supplier')
                ->with('success', 'Data supplier berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('report.laporan-data-supplier')
                ->with('error', 'Gagal menghapus data supplier: ' . $e->getMessage());
        }
    }

    /**
     * Get barang by kode (API endpoint)
     */
    public function getBarangByKode($kode)
    {
        try {
            $barang = Barang::where('kode_barang', $kode)->first();
            
            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang tidak ditemukan'
                ], 404);
            }
            
            // Only return barang that has harga_beli > 0 (from saldo awal)
            if ($barang->harga_beli <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang tidak memiliki harga beli (belum ada di saldo awal)'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'barang' => [
                    'id' => $barang->id,
                    'kode_barang' => $barang->kode_barang,
                    'nama_barang' => $barang->nama_barang,
                    'satuan' => $barang->satuan,
                    'harga_beli' => $barang->harga_beli,
                    'stok' => $barang->stok,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get barang by name (API endpoint for auto-fill)
     */
    public function getBarangByName(Request $request)
    {
        try {
            $nama = $request->input('nama');
            
            if (!$nama) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter nama diperlukan'
                ], 400);
            }
            
            // Return list of matching barangs to handle duplicate names
            $barangs = Barang::where('nama_barang', 'like', '%' . $nama . '%')->get();
            
            if ($barangs->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang tidak ditemukan'
                ], 404);
            }
            
            // Transform collection to array
            $result = $barangs->map(function ($barang) {
                return [
                    'id' => $barang->id,
                    'kode_barang' => $barang->kode_barang,
                    'nama_barang' => $barang->nama_barang,
                    'deskripsi' => $barang->deskripsi,
                    'kategori' => $barang->deskripsi, // Alias for frontend
                    'satuan' => $barang->satuan,
                    'harga_beli' => $barang->harga_beli,
                    'harga_jual' => $barang->harga_jual,
                    'stok' => $barang->stok,
                ];
            });

            return response()->json([
                'success' => true,
                'barang' => $result // Return list instead of single object
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer by name (API endpoint for auto-fill)
     */
    public function getCustomerByName(Request $request)
    {
        try {
            $nama = $request->input('nama');
            
            if (!$nama) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter nama diperlukan'
                ], 400);
            }
            
            $customer = Customer::where('nama_customer', $nama)->first();
            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'kode_customer' => $customer->kode_customer,
                    'nama_customer' => $customer->nama_customer,
                    'alamat' => $customer->alamat,
                    'telepon' => $customer->telepon,
                    'email' => $customer->email,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete customer data
     */
    public function destroyCustomer($id)
    {
        // Check if user has permission (admin or superadmin)
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Unauthorized access. Only admin and pimpinan can delete customer.');
        }

        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();

            return redirect()->route('report.laporan-data-customer')
                ->with('success', 'Data customer berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('report.laporan-data-customer')
                ->with('error', 'Gagal menghapus data customer: ' . $e->getMessage());
        }
    }
}
