<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    /**
     * Riwayat Seluruh Sesi Stock Opname
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', '');
        $endDate   = $request->get('end_date', '');
        $search    = $request->get('search', '');

        $query = StockOpname::with(['user', 'items.product'])
            ->latest('opname_date');

        if ($startDate) {
            $query->whereDate('opname_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('opname_date', '<=', $endDate);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $opnames = $query->paginate(15)->withQueryString();

        // Metrik Ringkasan
        $totalSessions = StockOpname::count();
        $totalCheckedItems = StockOpnameItem::count();
        $totalDiscrepancyItems = StockOpnameItem::where('difference', '!=', 0)->count();
        $lastOpname = StockOpname::latest('opname_date')->first();

        return view('stock-opname.index', compact(
            'opnames',
            'startDate',
            'endDate',
            'search',
            'totalSessions',
            'totalCheckedItems',
            'totalDiscrepancyItems',
            'lastOpname'
        ));
    }

    /**
     * Form Mulai Sesi Stock Opname Baru
     */
    public function create(Request $request)
    {
        $categoryId = $request->get('category_id', 'all');

        $query = Product::with(['category', 'baseUnit'])
            ->where('is_active', true)
            ->orderBy('name');

        if ($categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }

        $products   = $query->get();
        $categories = Category::orderBy('name')->get();

        return view('stock-opname.create', compact('products', 'categories', 'categoryId'));
    }

    /**
     * Simpan Sesi Stock Opname & Lakukan Penyesuaian Stok Otomatis
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'opname_date'               => ['required', 'date'],
            'notes'                     => ['nullable', 'string', 'max:500'],
            'items'                     => ['required', 'array', 'min:1'],
            'items.*.product_id'        => ['required', 'exists:products,id'],
            'items.*.physical_stock'    => ['required', 'numeric', 'min:0'],
            'items.*.notes'             => ['nullable', 'string', 'max:255'],
        ]);

        $stockOpname = DB::transaction(function () use ($validated) {
            $opname = StockOpname::create([
                'user_id'     => auth()->id(),
                'opname_date' => $validated['opname_date'],
                'status'      => 'COMPLETED',
                'notes'       => $validated['notes'] ?? 'Stock opname fisik gudang & rak kasir',
            ]);

            foreach ($validated['items'] as $itemData) {
                $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);
                $systemStock   = (float) $product->current_stock;
                $physicalStock = (float) $itemData['physical_stock'];
                $difference    = $physicalStock - $systemStock;

                StockOpnameItem::create([
                    'stock_opname_id' => $opname->id,
                    'product_id'      => $product->id,
                    'system_stock'    => $systemStock,
                    'physical_stock'  => $physicalStock,
                    'difference'      => $difference,
                    'notes'           => $itemData['notes'] ?? null,
                ]);

                // Jika ada selisih, update stok produk dan catat di StockMovement
                if (abs($difference) > 0.0001) {
                    $product->update(['current_stock' => $physicalStock]);

                    StockMovement::create([
                        'product_id'     => $product->id,
                        'user_id'        => auth()->id(),
                        'type'           => 'ADJUSTMENT',
                        'reference_type' => StockOpname::class,
                        'reference_id'   => $opname->id,
                        'quantity'       => $difference,
                        'stock_before'   => $systemStock,
                        'stock_after'    => $physicalStock,
                        'notes'          => 'Stock Opname: ' . ($itemData['notes'] ?: ($difference < 0 ? 'Penyesuaian stok berkurang' : 'Penyesuaian stok bertambah')),
                    ]);
                }
            }

            return $opname;
        });

        return redirect()->route('stock-opname.show', $stockOpname)
            ->with('success', 'Sesi Stock Opname berhasil disimpan dan stok produk telah disesuaikan.');
    }

    /**
     * Tampilan Rincian Hasil Sesi Stock Opname
     */
    public function show(StockOpname $stockOpname)
    {
        $stockOpname->load(['user', 'items.product.category', 'items.product.baseUnit']);

        $totalChecked       = $stockOpname->items->count();
        $totalMatched       = $stockOpname->items->where('difference', 0)->count();
        $totalDiscrepancies = $stockOpname->items->where('difference', '!=', 0)->count();

        // Hitung estimasi nilai selisih nominal (Rp) berdasarkan harga beli produk
        $totalDiscrepancyValue = $stockOpname->items->sum(function ($item) {
            return $item->difference * ($item->product->purchase_price ?? 0);
        });

        return view('stock-opname.show', compact(
            'stockOpname',
            'totalChecked',
            'totalMatched',
            'totalDiscrepancies',
            'totalDiscrepancyValue'
        ));
    }

    /**
     * Cetak Berita Acara Stock Opname
     */
    public function printSlip(StockOpname $stockOpname)
    {
        $stockOpname->load(['user', 'items.product.category', 'items.product.baseUnit']);

        return view('stock-opname.print', compact('stockOpname'));
    }
}