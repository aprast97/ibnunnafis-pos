<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    /**
     * Halaman Utama Kasir / POS
     */
    public function index()
    {
        $products = Product::with(['category', 'baseUnit'])
            ->where('is_active', true)
            ->where('current_stock', '>', 0)
            ->orderBy('name')
            ->get();

        // Metrik penjualan hari ini
        $todayCompletedSales = Sale::where('status', 'COMPLETED')
            ->whereDate('transaction_date', today());

        $todayRevenue   = (clone $todayCompletedSales)->sum('total_amount');
        $todayCount     = (clone $todayCompletedSales)->count();
        $todayItemsSold = SaleItem::whereHas('sale', function ($q) {
            $q->where('status', 'COMPLETED')->whereDate('transaction_date', today());
        })->sum('quantity');

        return view('pos.index', compact(
            'products',
            'todayRevenue',
            'todayCount',
            'todayItemsSold'
        ));
    }

    /**
     * Proses Checkout / Transaksi Penjualan (AJAX / Form Post)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'numeric', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.subtotal'   => ['required', 'numeric', 'min:0'],
            'total_amount'       => ['required', 'numeric', 'min:0'],
            'payment_amount'     => ['required', 'numeric', 'min:0'],
            'change_amount'      => ['required', 'numeric', 'min:0'],
        ]);

        if ($validated['payment_amount'] < $validated['total_amount']) {
            throw ValidationException::withMessages([
                'payment_amount' => 'Jumlah pembayaran kurang dari total belanja.',
            ]);
        }

        $sale = DB::transaction(function () use ($validated) {
            $transactionNumber = Sale::generateTransactionNumber();

            $sale = Sale::create([
                'user_id'            => auth()->id(),
                'transaction_number' => $transactionNumber,
                'transaction_date'   => now(),
                'total_amount'       => $validated['total_amount'],
                'payment_amount'     => $validated['payment_amount'],
                'change_amount'      => $validated['change_amount'],
                'status'             => 'COMPLETED',
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->current_stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => "Stok produk '{$product->name}' tidak mencukupi (sisa: {$product->current_stock}).",
                    ]);
                }

                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $product->id,
                    'unit_id'    => $product->base_unit_id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal'   => $item['subtotal'],
                ]);

                $stockBefore = $product->current_stock;
                $product->decrement('current_stock', $item['quantity']);
                $stockAfter = $product->fresh()->current_stock;

                StockMovement::create([
                    'product_id'     => $product->id,
                    'user_id'        => auth()->id(),
                    'type'           => 'OUT',
                    'reference_type' => Sale::class,
                    'reference_id'   => $sale->id,
                    'quantity'       => -$item['quantity'],
                    'stock_before'   => $stockBefore,
                    'stock_after'    => $stockAfter,
                    'notes'          => "Penjualan Kasir #{$sale->transaction_number}",
                ]);
            }

            return $sale;
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'            => true,
                'message'            => 'Transaksi penjualan berhasil diproses.',
                'sale_id'            => $sale->id,
                'transaction_number' => $sale->transaction_number,
                'receipt_url'        => route('transactions.receipt', $sale),
            ]);
        }

        return redirect()->route('transactions.receipt', $sale)
            ->with('success', "Transaksi #{$sale->transaction_number} berhasil disimpan!");
    }

    /**
     * Riwayat Seluruh Transaksi Penjualan
     */
    public function history(Request $request)
    {
        $search    = $request->get('search', '');
        $startDate = $request->get('start_date', '');
        $endDate   = $request->get('end_date', '');
        $status    = $request->get('status', 'all');

        $query = Sale::with(['user', 'items.product.baseUnit'])
            ->latest('transaction_date');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($startDate) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $sales = $query->paginate(15)->withQueryString();

        // Metrik Ringkasan
        $completedQuery = Sale::where('status', 'COMPLETED');
        $totalRevenue   = (clone $completedQuery)->sum('total_amount');
        $todayRevenue   = (clone $completedQuery)->whereDate('transaction_date', today())->sum('total_amount');
        $totalCount     = (clone $completedQuery)->count();
        $cancelledCount = Sale::where('status', 'CANCELLED')->count();

        return view('transactions.index', compact(
            'sales',
            'search',
            'startDate',
            'endDate',
            'status',
            'totalRevenue',
            'todayRevenue',
            'totalCount',
            'cancelledCount'
        ));
    }

    /**
     * Struk Digital / Preview Nota Transaksi
     */
    public function receipt(Sale $sale)
    {
        $sale->load(['user', 'items.product.baseUnit', 'items.unit']);

        return view('transactions.receipt', compact('sale'));
    }

    /**
     * Pembatalan Transaksi oleh Admin
     */
    public function cancel(Request $request, Sale $sale)
    {
        $request->validate([
            'cancellation_reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        if ($sale->isCancelled()) {
            return back()->with('error', 'Transaksi ini sudah pernah dibatalkan sebelumnya.');
        }

        DB::transaction(function () use ($request, $sale) {
            foreach ($sale->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);

                if ($product) {
                    $stockBefore = $product->current_stock;
                    $product->increment('current_stock', $item->quantity);
                    $stockAfter = $product->fresh()->current_stock;

                    StockMovement::create([
                        'product_id'     => $product->id,
                        'user_id'        => auth()->id(),
                        'type'           => 'ADJUSTMENT',
                        'reference_type' => Sale::class,
                        'reference_id'   => $sale->id,
                        'quantity'       => $item->quantity,
                        'stock_before'   => $stockBefore,
                        'stock_after'    => $stockAfter,
                        'notes'          => "Pembatalan Transaksi #{$sale->transaction_number} oleh Admin: " . $request->cancellation_reason,
                    ]);
                }
            }

            $sale->update([
                'status'              => 'CANCELLED',
                'canceled_at'         => now(),
                'canceled_by'         => auth()->id(),
                'cancellation_reason' => $request->cancellation_reason,
            ]);
        });

        return redirect()->route('transactions.index')
            ->with('success', "Transaksi #{$sale->transaction_number} berhasil dibatalkan dan stok dikembalikan ke sistem.");
    }
}