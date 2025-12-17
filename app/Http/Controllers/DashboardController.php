<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PembelianBahanBaku;
use App\Models\BarangDalamProses;
use App\Models\BarangJadi;
use App\Models\BarangJadiDetail;
use App\Models\PembelianBahanBakuDetail;
use App\Models\PemakaianBahanBaku;
use App\Models\PemakaianBarangDalamProses;
use App\Models\PenjualanBarangJadi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display dashboard with statistics
     */
    public function index()
    {
        // Total barang
        $totalBarang = Barang::count();

        // Total masuk - sum dari semua input transactions
        $totalMasuk = $this->calculateTotalMasuk();

        // Total keluar - sum dari semua output transactions
        $totalKeluar = $this->calculateTotalKeluar();

        // Total opname - count dari stock opname records
        $totalOpname = $this->calculateTotalOpname();

        // Transaction data for chart (last 30 days)
        $transactionData = $this->getTransactionChartData();

        return view('dashboard', compact(
            'totalBarang',
            'totalMasuk',
            'totalKeluar',
            'totalOpname',
            'transactionData'
        ));
    }

    /**
     * Calculate total masuk from all input transactions
     */
    private function calculateTotalMasuk()
    {
        // Pembelian Bahan Baku
        $pembelianBahanBaku = PembelianBahanBaku::sum('total_harga') ?? 0;
        
        // Barang Dalam Proses
        $barangDalamProses = BarangDalamProses::sum('total_harga') ?? 0;
        
        // Barang Jadi
        $barangJadi = BarangJadi::sum('total_harga') ?? 0;

        return $pembelianBahanBaku + $barangDalamProses + $barangJadi;
    }

    /**
     * Calculate total keluar from all output transactions
     */
    private function calculateTotalKeluar()
    {
        // Pemakaian Bahan Baku
        $pemakaianBahanBaku = PemakaianBahanBaku::sum('total_harga') ?? 0;
        
        // Pemakaian Barang Dalam Proses
        $pemakaianBarangDalamProses = PemakaianBarangDalamProses::sum('total_harga') ?? 0;
        
        // Penjualan Barang Jadi
        $penjualanBarangJadi = PenjualanBarangJadi::sum('total_harga') ?? 0;

        return $pemakaianBahanBaku + $pemakaianBarangDalamProses + $penjualanBarangJadi;
    }

    /**
     * Calculate total opname records
     */
    private function calculateTotalOpname()
    {
        // Check if stock_opname table exists
        try {
            return DB::table('stock_opnames')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get transaction data for chart (last 30 days)
     */
    private function getTransactionChartData()
    {
        $startDate = now()->subDays(29)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        // Helper function to get daily counts
        $getCounts = function ($model) use ($startDate, $endDate) {
            return $model::whereBetween('tanggal', [$startDate, $endDate])
                ->groupBy('tanggal')
                ->selectRaw('tanggal, count(*) as count')
                ->pluck('count', 'tanggal')
                ->toArray();
        };

        // Fetch all data in bulk
        $counts = [
            $getCounts(PembelianBahanBaku::class),
            $getCounts(BarangDalamProses::class),
            $getCounts(BarangJadi::class),
            $getCounts(PemakaianBahanBaku::class),
            $getCounts(PemakaianBarangDalamProses::class),
            $getCounts(PenjualanBarangJadi::class),
        ];

        $data = [];
        $labels = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d M');
            
            $total = 0;
            // Sum up counts for this specific date from all sources
            foreach ($counts as $sourceCounts) {
                // Determine if the key exists (handling string vs date object potentially, though format Y-m-d ensures string)
                // pluck returns strings as keys usually.
                if (isset($sourceCounts[$date])) {
                    $total += $sourceCounts[$date];
                }
            }
            
            $data[] = $total;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
