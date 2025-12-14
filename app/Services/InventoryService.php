<?php

namespace App\Services;

use App\Models\InventoryBatch;
use App\Models\SystemSetting;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Get current inventory valuation method.
     */
    public function getMethod(): string
    {
        $setting = SystemSetting::where('key', 'inventory_method')->first();
        return $setting ? $setting->value : 'fifo';
    }

    /**
     * Create a new inventory batch (Input).
     */
    public function createBatch(Barang $barang, float $qty, float $price, string $source, $sourceModel = null): InventoryBatch
    {
        // For Moving Average, we need to recalculate the average cost if we were using a pure single-field approach.
        // But since we are required to keep batches for FIFO/LIFO, we create the batch regardless.
        // For Weighted Average reporting, we can calculate on the fly or maintain a running average.
        // Requirement: "Create a new inventory batch record... For EVERY stock increase... NEVER merge batches."

        $batch = new InventoryBatch();
        $batch->barang_id = $barang->id;
        $batch->tanggal_masuk = now();
        $batch->qty_awal = $qty;
        $batch->qty_sisa = $qty;
        $batch->harga_beli = $price;
        $batch->sumber = $source;

        if ($sourceModel) {
            $batch->source()->associate($sourceModel);
        }

        $batch->save();

        return $batch;
    }

    /**
     * Consume inventory and calculate HPP (Output).
     * Returns total cost of goods sold.
     */
    public function consumeInventory(Barang $barang, float $qty): array
    {
        $method = $this->getMethod();

        if ($method === 'average') {
            return $this->consumeAverage($barang, $qty);
        } elseif ($method === 'lifo') {
            return $this->consumeLifo($barang, $qty);
        } else {
            return $this->consumeFifo($barang, $qty);
        }
    }

    private function consumeFifo(Barang $barang, float $qty): array
    {
        $remainingQty = $qty;
        $totalCost = 0;

        // Lock batches to prevent race conditions
        $batches = InventoryBatch::where('barang_id', $barang->id)
            ->where('qty_sisa', '>', 0)
            ->orderBy('tanggal_masuk', 'asc')
            ->orderBy('id', 'asc')
            ->lockForUpdate()
            ->get();

        foreach ($batches as $batch) {
            if ($remainingQty <= 0) break;

            $take = min($remainingQty, $batch->qty_sisa);

            $batch->qty_sisa -= $take;
            $batch->save();

            $totalCost += ($take * $batch->harga_beli);
            $remainingQty -= $take;
        }

        if ($remainingQty > 0) {
            // Not enough stock in batches, but system allowed transaction (negative stock scenario)
            // Fallback: use current master price or last known price
            // Per requirement "Prevent negative stock", controller should have checked total stock first.
            // But if we are here, we must return a cost.
            $fallbackPrice = $barang->harga_beli; // Standard cost
            $totalCost += ($remainingQty * $fallbackPrice);
        }

        $unitCost = $qty > 0 ? $totalCost / $qty : 0;

        return [
            'total_cost' => $totalCost,
            'unit_cost' => $unitCost
        ];
    }

    private function consumeLifo(Barang $barang, float $qty): array
    {
        $remainingQty = $qty;
        $totalCost = 0;

        $batches = InventoryBatch::where('barang_id', $barang->id)
            ->where('qty_sisa', '>', 0)
            ->orderBy('tanggal_masuk', 'desc')
            ->orderBy('id', 'desc')
            ->lockForUpdate()
            ->get();

        foreach ($batches as $batch) {
            if ($remainingQty <= 0) break;

            $take = min($remainingQty, $batch->qty_sisa);

            $batch->qty_sisa -= $take;
            $batch->save();

            $totalCost += ($take * $batch->harga_beli);
            $remainingQty -= $take;
        }

        if ($remainingQty > 0) {
            $fallbackPrice = $barang->harga_beli;
            $totalCost += ($remainingQty * $fallbackPrice);
        }

        $unitCost = $qty > 0 ? $totalCost / $qty : 0;

        return [
            'total_cost' => $totalCost,
            'unit_cost' => $unitCost
        ];
    }

    private function consumeAverage(Barang $barang, float $qty): array
    {
        // Moving Average Logic:
        // Calculate total value of available inventory
        $totalValue = InventoryBatch::where('barang_id', $barang->id)
            ->where('qty_sisa', '>', 0)
            ->sum(DB::raw('qty_sisa * harga_beli'));

        $totalQty = InventoryBatch::where('barang_id', $barang->id)
            ->sum('qty_sisa');

        // Avoid division by zero
        if ($totalQty <= 0) {
            $avgPrice = $barang->harga_beli; // Fallback to master price
        } else {
            $avgPrice = $totalValue / $totalQty;
        }

        $totalCost = $avgPrice * $qty;

        // For Average, we still need to deplete batches to maintain consistency
        // with the "qty_sisa" concept, even though the price is averaged.
        // Usually, Average method consumes from a pool, but if we mix methods or switch (hypothetically),
        // or just to maintain the same table structure, we deplete FIFO-like to clear old records.
        // Prompt says: "Inventory depletion must follow accounting rules... Average: weighted average"
        // It does not specify *which* batch to reduce qty from for Average.
        // Standard practice for system that tracks batches: Deplete FIFO for quantity tracking, but apply Average Cost.

        $this->consumeFifoForQtyOnly($barang, $qty);

        return [
            'total_cost' => $totalCost,
            'unit_cost' => $avgPrice
        ];
    }

    private function consumeFifoForQtyOnly(Barang $barang, float $qty)
    {
        $remainingQty = $qty;

        $batches = InventoryBatch::where('barang_id', $barang->id)
            ->where('qty_sisa', '>', 0)
            ->orderBy('tanggal_masuk', 'asc')
            ->orderBy('id', 'asc')
            ->lockForUpdate()
            ->get();

        foreach ($batches as $batch) {
            if ($remainingQty <= 0) break;
            $take = min($remainingQty, $batch->qty_sisa);
            $batch->qty_sisa -= $take;
            $batch->save();
            $remainingQty -= $take;
        }
    }
}
