<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\StockMovement;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->get('search', ''));
        $categoryId = $request->get('category_id');
        $stockStatus = $request->get('stock_status', 'all');
        $activeStatus = $request->get('is_active', 'all');

        $query = Product::with(['category', 'baseUnit', 'productUnits.unit']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($categoryId && $categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }

        if ($activeStatus !== 'all' && in_array($activeStatus, ['0', '1'], true)) {
            $query->where('is_active', (bool)$activeStatus);
        }

        if ($stockStatus === 'aman') {
            $query->whereColumn('current_stock', '>', 'minimum_stock');
        } elseif ($stockStatus === 'menipis') {
            $query->whereColumn('current_stock', '<=', 'minimum_stock')
                  ->where('current_stock', '>', 0);
        } elseif ($stockStatus === 'habis') {
            $query->where('current_stock', '<=', 0);
        }

        $products = $query->orderBy('name')->paginate(15)->withQueryString();

        // Metrics for summary cards
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $lowStockCount = Product::where('is_active', true)
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('current_stock', '>', 0)
            ->count();
        $outOfStockCount = Product::where('is_active', true)
            ->where('current_stock', '<=', 0)
            ->count();

        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('products.index', compact(
            'products',
            'categories',
            'units',
            'search',
            'categoryId',
            'stockStatus',
            'activeStatus',
            'totalProducts',
            'activeProducts',
            'lowStockCount',
            'outOfStockCount'
        ));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('products.create', compact('categories', 'units'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:products,sku'],
            'category_id' => ['required', 'exists:categories,id'],
            'base_unit_id' => ['required', 'exists:units,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'current_stock' => ['nullable', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'conversion_unit_id' => ['nullable', 'different:base_unit_id', 'exists:units,id'],
            'conversion_factor' => ['nullable', 'required_with:conversion_unit_id', 'numeric', 'min:1'],
        ], [
            'name.required' => 'Nama produk wajib diisi.',
            'sku.unique' => 'SKU/Barcode sudah digunakan pada produk lain.',
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'base_unit_id.required' => 'Satuan dasar penjualan wajib dipilih.',
            'purchase_price.required' => 'Harga beli wajib diisi.',
            'selling_price.required' => 'Harga jual wajib diisi.',
            'conversion_unit_id.different' => 'Satuan konversi barang masuk harus berbeda dengan satuan dasar penjualan.',
            'conversion_factor.required_with' => 'Nilai konversi wajib diisi jika satuan pembelian dipilih.',
        ]);

        $initialStock = (float)($validated['current_stock'] ?? 0);
        $imagePath = $request->file('image')?->store('products', 'public');

        DB::transaction(function () use ($validated, $initialStock, $imagePath) {
            $product = Product::create([
                'category_id' => $validated['category_id'],
                'base_unit_id' => $validated['base_unit_id'],
                'name' => $validated['name'],
                'image_path' => $imagePath,
                'sku' => $validated['sku'] ?: null,
                'purchase_price' => $validated['purchase_price'],
                'selling_price' => $validated['selling_price'],
                'current_stock' => $initialStock,
                'minimum_stock' => $validated['minimum_stock'] ?? 5,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Save multi-unit conversion if configured
            if (!empty($validated['conversion_unit_id']) && !empty($validated['conversion_factor'])) {
                ProductUnit::create([
                    'product_id' => $product->id,
                    'unit_id' => $validated['conversion_unit_id'],
                    'conversion_factor' => $validated['conversion_factor'],
                ]);
            }

            // Create initial stock movement if initial stock > 0
            if ($initialStock > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'IN',
                    'reference_type' => 'InitialStock',
                    'reference_id' => null,
                    'quantity' => $initialStock,
                    'stock_before' => 0,
                    'stock_after' => $initialStock,
                    'notes' => 'Saldo awal produk saat pendaftaran',
                ]);
            }
        });

        return redirect()->route('products.index')
            ->with('success', "Produk '{$validated['name']}' berhasil ditambahkan ke katalog.");
    }

    public function show(Product $product): View
    {
        $product->load([
            'category',
            'baseUnit',
            'productUnits.unit',
            'stockMovements' => fn($q) => $q->with('user')->latest()->take(10),
        ]);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $conversion = $product->productUnits()->first();

        return view('products.edit', compact('product', 'categories', 'units', 'conversion'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:products,sku,' . $product->id],
            'category_id' => ['required', 'exists:categories,id'],
            'base_unit_id' => ['required', 'exists:units,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'conversion_unit_id' => ['nullable', 'different:base_unit_id', 'exists:units,id'],
            'conversion_factor' => ['nullable', 'required_with:conversion_unit_id', 'numeric', 'min:1'],
        ], [
            'name.required' => 'Nama produk wajib diisi.',
            'sku.unique' => 'SKU/Barcode sudah digunakan pada produk lain.',
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'base_unit_id.required' => 'Satuan dasar penjualan wajib dipilih.',
            'purchase_price.required' => 'Harga beli wajib diisi.',
            'selling_price.required' => 'Harga jual wajib diisi.',
            'conversion_unit_id.different' => 'Satuan konversi barang masuk harus berbeda dengan satuan dasar penjualan.',
            'conversion_factor.required_with' => 'Nilai konversi wajib diisi jika satuan pembelian dipilih.',
        ]);

        $newImagePath = $request->file('image')?->store('products', 'public');

        DB::transaction(function () use ($validated, $product, $newImagePath) {
            if ($newImagePath && $product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            $product->update([
                'category_id' => $validated['category_id'],
                'base_unit_id' => $validated['base_unit_id'],
                'name' => $validated['name'],
                'image_path' => $newImagePath ?: $product->image_path,
                'sku' => $validated['sku'] ?: null,
                'purchase_price' => $validated['purchase_price'],
                'selling_price' => $validated['selling_price'],
                'minimum_stock' => $validated['minimum_stock'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Update or remove conversion unit
            if (!empty($validated['conversion_unit_id']) && !empty($validated['conversion_factor'])) {
                ProductUnit::updateOrCreate(
                    ['product_id' => $product->id],
                    [
                        'unit_id' => $validated['conversion_unit_id'],
                        'conversion_factor' => $validated['conversion_factor'],
                    ]
                );
            } else {
                $product->productUnits()->delete();
            }
        });

        return redirect()->route('products.index')
            ->with('success', "Data produk '{$product->name}' berhasil diperbarui.");
    }

    public function toggleStatus(Product $product): RedirectResponse
    {
        $product->is_active = !$product->is_active;
        $product->save();

        $statusStr = $product->is_active ? 'Diaktifkan' : 'Dinonaktifkan';

        return redirect()->route('products.index')
            ->with('success', "Produk '{$product->name}' berhasil {$statusStr}.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        $hasSales = $product->saleItems()->exists();
        $hasPurchases = $product->purchaseItems()->exists();
        $movementCount = $product->stockMovements()->where('reference_type', '!=', 'InitialStock')->count();

        if ($hasSales || $hasPurchases || $movementCount > 0) {
            return redirect()->route('products.index')
                ->with('error', "Produk '{$product->name}' tidak dapat dihapus karena sudah memiliki histori transaksi penjualan/pembelian. Silakan gunakan fitur Nonaktifkan Produk untuk menghentikan penjualan.");
        }

        DB::transaction(function () use ($product) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $product->stockMovements()->delete();
            $product->productUnits()->delete();
            $product->delete();
        });

        return redirect()->route('products.index')
            ->with('success', "Produk '{$product->name}' berhasil dihapus dari sistem.");
    }
}
