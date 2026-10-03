<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Role;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $kasirRole = Role::create([
            'name' => 'Kasir',
            'description' => 'Petugas Kasir & Operasional Harian Kantin',
        ]);

        $adminRole = Role::create([
            'name' => 'Admin',
            'description' => 'Monitoring Operasional & Laporan Kantin',
        ]);

        // 2. Users (Fixed accounts: 2 Kasir, 1 Admin as requested)
        $admin = User::create([
            'role_id' => $adminRole->id,
            'name' => 'Ummi Hj. Romlah Dana Sidiq Lc',
            'username' => '84921',
            'password' => Hash::make('84921'),
        ]);

        $kasir1 = User::create([
            'role_id' => $kasirRole->id,
            'name' => 'Raudhatul Abila Sunarya',
            'username' => '39147',
            'password' => Hash::make('39147'),
        ]);

        $kasir2 = User::create([
            'role_id' => $kasirRole->id,
            'name' => 'Alya Nur Fadhilah',
            'username' => '72583',
            'password' => Hash::make('72583'),
        ]);

        // 3. Categories
        $catMakanan = Category::create(['name' => 'Makanan Ringan & Berat', 'description' => 'Mie instan, gorengan, roti']);
        $catMinuman = Category::create(['name' => 'Minuman', 'description' => 'Minuman dingin dan hangat']);
        $catSnack   = Category::create(['name' => 'Snack & Camilan', 'description' => 'Kerupuk, biskuit, snack santri']);
        $catAtk     = Category::create(['name' => 'Alat Tulis & Kebutuhan', 'description' => 'Buku tulis, pulpen, sabun']);
        $catKitab   = Category::create(['name' => 'Kitab & Buku Agama', 'description' => 'Kitab kuning dan buku panduan']);

        // 4. Units
        $unitPcs    = Unit::create(['name' => 'Pcs', 'symbol' => 'pcs']);
        $unitBotol  = Unit::create(['name' => 'Botol', 'symbol' => 'btl']);
        $unitBungkus = Unit::create(['name' => 'Bungkus', 'symbol' => 'bks']);
        $unitBuah   = Unit::create(['name' => 'Buah', 'symbol' => 'bh']);
        $unitDus    = Unit::create(['name' => 'Dus', 'symbol' => 'dus']);
        $unitPak    = Unit::create(['name' => 'Pak', 'symbol' => 'pak']);
        $unitKarton = Unit::create(['name' => 'Karton', 'symbol' => 'krt']);
        $unitKrat   = Unit::create(['name' => 'Krat', 'symbol' => 'krat']);

        // 5. Suppliers
        $supIndofood = Supplier::create([
            'name' => 'PT Indofood Sukses Makmur',
            'phone' => '0812-3456-7890',
            'address' => 'Jl. Raya Bogor KM 31, Cimanggis, Depok',
        ]);

        $supSosro = Supplier::create([
            'name' => 'PT Sinar Sosro Distributor',
            'phone' => '0813-9876-5432',
            'address' => 'Jl. Margonda Raya No. 142, Depok',
        ]);

        $supSnack = Supplier::create([
            'name' => 'CV Berkah Snack Nusantara',
            'phone' => '0857-1122-3344',
            'address' => 'Pasar Kemiri Muka Blok C No. 12, Beji, Depok',
        ]);

        $supPasar = Supplier::create([
            'name' => 'Toko Pasar & Kelontong Depok',
            'phone' => '0878-8899-0011',
            'address' => 'Pasar Kemiri, Beji, Depok',
        ]);

        // 6. Products & Initial Stock Movements
        $productsData = [
            [
                'category_id' => $catMakanan->id,
                'base_unit_id' => $unitPcs->id,
                'name' => 'Indomie Goreng Special',
                'sku' => '899886611001',
                'purchase_price' => 2450,
                'selling_price' => 3500,
                'current_stock' => 45,
                'minimum_stock' => 15,
                'conversion_unit' => $unitDus->id,
                'conversion_factor' => 40,
            ],
            [
                'category_id' => $catMakanan->id,
                'base_unit_id' => $unitPcs->id,
                'name' => 'Indomie Kuah Ayam Bawang',
                'sku' => '899886611002',
                'purchase_price' => 2450,
                'selling_price' => 3500,
                'current_stock' => 12, // Low stock!
                'minimum_stock' => 15,
                'conversion_unit' => $unitDus->id,
                'conversion_factor' => 40,
            ],
            [
                'category_id' => $catMinuman->id,
                'base_unit_id' => $unitBotol->id,
                'name' => 'Teh Botol Sosro 330ml',
                'sku' => '899886611003',
                'purchase_price' => 2850,
                'selling_price' => 4000,
                'current_stock' => 5, // Low stock!
                'minimum_stock' => 10,
                'conversion_unit' => $unitKrat->id,
                'conversion_factor' => 24,
            ],
            [
                'category_id' => $catMinuman->id,
                'base_unit_id' => $unitBotol->id,
                'name' => 'Air Mineral Prima 600ml',
                'sku' => '899886611004',
                'purchase_price' => 1850,
                'selling_price' => 3000,
                'current_stock' => 36,
                'minimum_stock' => 12,
                'conversion_unit' => $unitKarton->id,
                'conversion_factor' => 24,
            ],
            [
                'category_id' => $catMakanan->id,
                'base_unit_id' => $unitBuah->id,
                'name' => 'Gorengan Bakwan Sayur',
                'sku' => null,
                'purchase_price' => 750,
                'selling_price' => 1500,
                'current_stock' => 25,
                'minimum_stock' => 10,
                'conversion_unit' => null,
                'conversion_factor' => null,
            ],
            [
                'category_id' => $catMakanan->id,
                'base_unit_id' => $unitBuah->id,
                'name' => 'Gorengan Tahu Isi Pedas',
                'sku' => null,
                'purchase_price' => 750,
                'selling_price' => 1500,
                'current_stock' => 0, // Out of stock!
                'minimum_stock' => 10,
                'conversion_unit' => null,
                'conversion_factor' => null,
            ],
            [
                'category_id' => $catSnack->id,
                'base_unit_id' => $unitBungkus->id,
                'name' => 'Kerupuk Ikan Kaleng',
                'sku' => '899886611005',
                'purchase_price' => 1150,
                'selling_price' => 2000,
                'current_stock' => 20,
                'minimum_stock' => 8,
                'conversion_unit' => $unitPak->id,
                'conversion_factor' => 10,
            ],
            [
                'category_id' => $catAtk->id,
                'base_unit_id' => $unitPcs->id,
                'name' => 'Buku Tulis Santri 38 Lembar',
                'sku' => '899886611006',
                'purchase_price' => 3450,
                'selling_price' => 5000,
                'current_stock' => 30,
                'minimum_stock' => 10,
                'conversion_unit' => $unitPak->id,
                'conversion_factor' => 10,
            ],
            [
                'category_id' => $catKitab->id,
                'base_unit_id' => $unitPcs->id,
                'name' => 'Kitab Safinatun Najah',
                'sku' => '899886611007',
                'purchase_price' => 11850,
                'selling_price' => 16000,
                'current_stock' => 15,
                'minimum_stock' => 5,
                'conversion_unit' => null,
                'conversion_factor' => null,
            ],
        ];

        foreach ($productsData as $data) {
            $convUnit = $data['conversion_unit'];
            $convFactor = $data['conversion_factor'];
            unset($data['conversion_unit'], $data['conversion_factor']);

            $product = Product::create($data);

            if ($convUnit && $convFactor) {
                ProductUnit::create([
                    'product_id' => $product->id,
                    'unit_id' => $convUnit,
                    'conversion_factor' => $convFactor,
                ]);
            }

            // Create initial stock movement if initial stock > 0
            if ($product->current_stock > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $admin->id,
                    'type' => 'IN',
                    'reference_type' => 'InitialStock',
                    'reference_id' => null,
                    'quantity' => $product->current_stock,
                    'stock_before' => 0,
                    'stock_after' => $product->current_stock,
                    'notes' => 'Saldo awal stok sistem',
                ]);
            }
        }
    }
}