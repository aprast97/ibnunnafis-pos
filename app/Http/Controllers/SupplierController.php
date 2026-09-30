<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->get('search', ''));

        $query = Supplier::withCount('purchases');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('suppliers.index', compact('suppliers', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama supplier/toko wajib diisi.',
            'name.max' => 'Nama supplier maksimal 150 karakter.',
            'phone.max' => 'Nomor kontak maksimal 30 karakter.',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', "Mitra Supplier '{$validated['name']}' berhasil ditambahkan.");
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama supplier/toko wajib diisi.',
            'name.max' => 'Nama supplier maksimal 150 karakter.',
            'phone.max' => 'Nomor kontak maksimal 30 karakter.',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', "Data Supplier '{$supplier->name}' berhasil diperbarui.");
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $purchaseCount = $supplier->purchases()->count();

        if ($purchaseCount > 0) {
            return redirect()->route('suppliers.index')
                ->with('error', "Supplier '{$supplier->name}' tidak dapat dihapus karena memiliki riwayat {$purchaseCount} faktur pengadaan barang masuk.");
        }

        $name = $supplier->name;
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', "Supplier '{$name}' berhasil dihapus.");
    }
}
