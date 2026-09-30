<?php

namespace App\Http\Controllers;

use App\Models\ProductUnit;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->get('search', ''));

        $query = Unit::withCount(['products', 'productUnits']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('symbol', 'like', "%{$search}%");
            });
        }

        $units = $query->orderBy('name')->get();

        $conversions = ProductUnit::with(['product.baseUnit', 'unit'])
            ->latest()
            ->take(10)
            ->get();

        return view('units.index', compact('units', 'conversions', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:units,name'],
            'symbol' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required' => 'Nama satuan wajib diisi.',
            'name.unique' => 'Nama satuan sudah digunakan.',
            'name.max' => 'Nama satuan maksimal 50 karakter.',
        ]);

        Unit::create($validated);

        return redirect()->route('units.index')
            ->with('success', "Satuan '{$validated['name']}' berhasil ditambahkan.");
    }

    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:units,name,' . $unit->id],
            'symbol' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required' => 'Nama satuan wajib diisi.',
            'name.unique' => 'Nama satuan sudah digunakan.',
            'name.max' => 'Nama satuan maksimal 50 karakter.',
        ]);

        $unit->update($validated);

        return redirect()->route('units.index')
            ->with('success', "Satuan '{$unit->name}' berhasil diperbarui.");
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        $baseCount = $unit->products()->count();
        $convCount = $unit->productUnits()->count();
        $totalUsage = $baseCount + $convCount;

        if ($totalUsage > 0) {
            return redirect()->route('units.index')
                ->with('error', "Satuan '{$unit->name}' tidak dapat dihapus karena masih digunakan pada {$totalUsage} konfigurasi produk (satuan dasar/konversi).");
        }

        $name = $unit->name;
        $unit->delete();

        return redirect()->route('units.index')
            ->with('success', "Satuan '{$name}' berhasil dihapus.");
    }
}
