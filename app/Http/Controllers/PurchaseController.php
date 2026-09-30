<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $supplierId = $request->input('supplier_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Overview metrics
        $thisMonthTotal = Purchase::whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year)
            ->sum('total_amount');

        $totalInvoicesCount = Purchase::count();
        $totalItemsReceived = PurchaseItem::sum('quantity_in_base_unit');
        $activeSuppliersCount = Supplier::count();

        // Query with filters
        $query = Purchase::with(['supplier', 'user', 'items.product', 'items.unit'])
            ->latest('purchase_date');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('supplier', fn($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($supplierId && $supplierId !== 'all') {
            $query->where('supplier_id', $supplierId);
        }

        if ($startDate) {
            $query->whereDate('purchase_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('purchase_date', '<=', $endDate);
        }

        $purchases = $query->paginate(15)->withQueryString();
        $suppliers = Supplier::orderBy('name')->get();

        return view('purchases.index', compact(
            'purchases',
            'suppliers',
            'search',
            'supplierId',
            'startDate',
            'endDate',
            'thisMonthTotal',
            'totalInvoicesCount',
            'totalItemsReceived',
            'activeSuppliersCount'
        ));
    }

    public function create(Request $request): View
    {
        $autoReceiptNumber = Purchase::generateReceiptNumber();
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::with(['category', 'baseUnit', 'productUnits.unit'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $productsJson = $products->map(function ($p) {
            $units = [
                [
                    'id' => $p->base_unit_id,
                    'name' => $p->baseUnit->name ?? 'Pcs',
                    'symbol' => $p->baseUnit->symbol ?? 'pcs',
                    'conversion_factor' => 1,
                    'is_base' => true,
                    'label' => ($p->baseUnit->name ?? 'Pcs') . ' (Satuan Dasar / Eceran)',
                ]
            ];

            foreach ($p->productUnits as $pu) {
                $units[] = [
                    'id' => $pu->unit_id,
                    'name' => $pu->unit->name,
                    'symbol' => $pu->unit->symbol,
                    'conversion_factor' => (float) $pu->conversion_factor,
                    'is_base' => false,
                    'label' => $pu->unit->name . " (1 {$pu->unit->name} = " . (int)$pu->conversion_factor . " " . ($p->baseUnit->name ?? 'Pcs') . ")",
                ];
            }

            return [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'category' => $p->category->name ?? 'Umum',
                'current_stock' => (float) $p->current_stock,
                'base_unit_id' => $p->base_unit_id,
                'base_unit_name' => $p->baseUnit->name ?? 'Pcs',
                'purchase_price' => (float) $p->purchase_price,
                'units' => $units,
            ];
        });

        $preselectedProductId = $request->query('product_id');

        return view('purchases.create', compact(
            'autoReceiptNumber',
            'suppliers',
            'products',
            'productsJson',
            'preselectedProductId'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'receipt_number' => ['nullable', 'string', 'max:100'],
            'purchase_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.unit_id' => ['required', 'exists:units,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.purchase_price' => ['required', 'numeric', 'min:0'],
        ], [
            'supplier_id.required' => 'Silakan pilih supplier mitra pengirim barang.',
            'supplier_id.exists' => 'Supplier yang dipilih tidak valid.',
            'purchase_date.required' => 'Tanggal pembelian/faktur wajib diisi.',
            'items.required' => 'Daftar barang masuk minimal berisi 1 item.',
            'items.min' => 'Daftar barang masuk minimal berisi 1 item.',
            'items.*.product_id.required' => 'Pilih produk pada setiap baris barang.',
            'items.*.unit_id.required' => 'Pilih satuan barang.',
            'items.*.quantity.required' => 'Jumlah barang wajib diisi.',
            'items.*.quantity.gt' => 'Jumlah barang harus lebih besar dari 0.',
            'items.*.purchase_price.required' => 'Harga beli barang wajib diisi.',
            'items.*.purchase_price.min' => 'Harga beli tidak boleh bernilai negatif.',
        ]);

        $purchase = DB::transaction(function () use ($validated) {
            $receiptNumber = !empty($validated['receipt_number'])
                ? trim($validated['receipt_number'])
                : Purchase::generateReceiptNumber();

            $supplier = Supplier::findOrFail($validated['supplier_id']);

            $purchase = Purchase::create([
                'supplier_id' => $supplier->id,
                'user_id' => auth()->id(),
                'receipt_number' => $receiptNumber,
                'purchase_date' => $validated['purchase_date'],
                'total_amount' => 0,
                'status' => 'COMPLETED',
                'notes' => $validated['notes'] ?? null,
            ]);

            $grandTotal = 0;

            foreach ($validated['items'] as $itemData) {
                $product = Product::with(['baseUnit', 'productUnits.unit'])
                    ->lockForUpdate()
                    ->findOrFail($itemData['product_id']);

                $unitId = (int) $itemData['unit_id'];
                $unit = Unit::findOrFail($unitId);
                $qty = (float) $itemData['quantity'];
                $price = (float) $itemData['purchase_price'];
                $subtotal = $qty * $price;
                $grandTotal += $subtotal;

                // Determine conversion factor
                $conversionFactor = 1.0;
                if ($unitId === (int) $product->base_unit_id) {
                    $conversionFactor = 1.0;
                } else {
                    $productUnit = $product->productUnits->firstWhere('unit_id', $unitId);
                    if ($productUnit) {
                        $conversionFactor = (float) $productUnit->conversion_factor;
                    }
                }

                $quantityInBaseUnit = $qty * $conversionFactor;

                // Create PurchaseItem
                $purchase->items()->create([
                    'product_id' => $product->id,
                    'unit_id' => $unitId,
                    'quantity' => $qty,
                    'conversion_factor' => $conversionFactor,
                    'quantity_in_base_unit' => $quantityInBaseUnit,
                    'purchase_price' => $price,
                    'subtotal' => $subtotal,
                ]);

                // Update product current stock
                $stockBefore = (float) $product->current_stock;
                $stockAfter = $stockBefore + $quantityInBaseUnit;
                $product->update([
                    'current_stock' => $stockAfter,
                ]);

                // Create StockMovement audit trail
                $noteConversion = $conversionFactor > 1 
                    ? " (= {$quantityInBaseUnit} " . ($product->baseUnit->name ?? 'Pcs') . ")" 
                    : "";

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'IN',
                    'reference_type' => 'Purchase',
                    'reference_id' => $purchase->id,
                    'quantity' => $quantityInBaseUnit,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'notes' => "Barang Masuk #{$receiptNumber} ({$supplier->name}): +{$qty} {$unit->name}{$noteConversion}",
                    'created_at' => $purchase->purchase_date,
                ]);
            }

            $purchase->update(['total_amount' => $grandTotal]);

            return $purchase;
        });

        return redirect()->route('purchases.show', $purchase)
            ->with('success', "Faktur barang masuk #{$purchase->receipt_number} berhasil dicatat. Stok barang dagangan telah ditambahkan ke sistem.");
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load([
            'supplier',
            'user',
            'items.product.category',
            'items.product.baseUnit',
            'items.unit',
        ]);

        return view('purchases.show', compact('purchase'));
    }

    public function printSlip(Purchase $purchase): View
    {
        $purchase->load([
            'supplier',
            'user',
            'items.product.baseUnit',
            'items.unit',
        ]);

        return view('purchases.print', compact('purchase'));
    }
}
