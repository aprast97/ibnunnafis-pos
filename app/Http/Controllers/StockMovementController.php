<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function index(Request $request): View
    {
        $productId = $request->input('product_id');
        $type = $request->input('type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $search = $request->input('search');

        // Statistics
        $totalInQty = StockMovement::where('type', 'IN')->sum('quantity');
        $totalOutQty = StockMovement::where('type', 'OUT')->sum('quantity');
        $totalAdjustCount = StockMovement::where('type', 'ADJUSTMENT')->count();
        $totalMovementsCount = StockMovement::count();

        // Query
        $query = StockMovement::with(['product.category', 'product.baseUnit', 'user'])
            ->latest('created_at');

        if ($productId && $productId !== 'all') {
            $query->where('product_id', $productId);
        }

        if ($type && $type !== 'all') {
            $query->where('type', strtoupper($type));
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                  ->orWhere('reference_type', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                         ->orWhere('sku', 'like', "%{$search}%");
                  });
            });
        }

        $movements = $query->paginate(20)->withQueryString();
        $products = Product::orderBy('name')->get();
        $selectedProduct = ($productId && $productId !== 'all') ? Product::with('baseUnit')->find($productId) : null;

        return view('stock-movements.index', compact(
            'movements',
            'products',
            'selectedProduct',
            'productId',
            'type',
            'startDate',
            'endDate',
            'search',
            'totalInQty',
            'totalOutQty',
            'totalAdjustCount',
            'totalMovementsCount'
        ));
    }
}
