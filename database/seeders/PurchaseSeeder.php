<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        if (Purchase::count() > 0) {
            return;
        }

        $kasir = User::where('username', '39147')->first() ?? User::whereHas('role', fn($q) => $q->where('name', 'Kasir'))->first();
        $supplierIndofood = Supplier::where('name', 'like', '%Indofood%')->first() ?? Supplier::first();
        $dusUnit = Unit::where('name', 'Dus')->first();
        $indomie = Product::where('name', 'like', '%Indomie%')->first();

        if (!$kasir || !$supplierIndofood || !$dusUnit || !$indomie) {
            return;
        }

        DB::transaction(function () use ($kasir, $supplierIndofood, $dusUnit, $indomie) {
            $purchaseDate = now()->subDays(1)->setTime(10, 15);
            $receiptNumber = 'BM-' . $purchaseDate->format('Ymd') . '-0001';

            $purchase = Purchase::create([
                'supplier_id' => $supplierIndofood->id,
                'user_id' => $kasir->id,
                'receipt_number' => $receiptNumber,
                'purchase_date' => $purchaseDate,
                'total_amount' => 220000,
                'status' => 'COMPLETED',
                'notes' => 'Penerimaan pasokan barang masuk 2 dus Indomie Goreng Special',
            ]);

            $purchase->items()->create([
                'product_id' => $indomie->id,
                'unit_id' => $dusUnit->id,
                'quantity' => 2,
                'conversion_factor' => 40,
                'quantity_in_base_unit' => 80,
                'purchase_price' => 110000,
                'subtotal' => 220000,
            ]);

            $before = (float) $indomie->current_stock;
            $after = $before + 80;
            $indomie->update(['current_stock' => $after]);

            StockMovement::create([
                'product_id' => $indomie->id,
                'user_id' => $kasir->id,
                'type' => 'IN',
                'reference_type' => 'Purchase',
                'reference_id' => $purchase->id,
                'quantity' => 80,
                'stock_before' => $before,
                'stock_after' => $after,
                'notes' => "Barang Masuk #{$receiptNumber} ({$supplierIndofood->name}): +2 Dus (= 80 Pcs)",
                'created_at' => $purchaseDate,
            ]);
        });
    }
}
