<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Purchase;
use App\Models\Role;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected User $kasir;
    protected User $admin;
    protected Supplier $supplier;
    protected Category $category;
    protected Unit $unitPcs;
    protected Unit $unitDus;
    protected Product $productIndomie;
    protected Product $productTeh;

    protected function setUp(): void
    {
        parent::setUp();

        $kasirRole = Role::create(['name' => 'Kasir', 'description' => 'Kasir']);
        $adminRole = Role::create(['name' => 'Admin', 'description' => 'Admin']);

        $this->kasir = User::create([
            'role_id' => $kasirRole->id,
            'name' => 'Raudhatul Abila',
            'username' => '39147',
            'email' => 'kasir@ibnunnafis.com',
            'password' => bcrypt('39147'),
        ]);

        $this->admin = User::create([
            'role_id' => $adminRole->id,
            'name' => 'Ummi Hj. Romlah',
            'username' => '84921',
            'email' => 'admin@ibnunnafis.com',
            'password' => bcrypt('84921'),
        ]);

        $this->supplier = Supplier::create([
            'name' => 'PT Indofood Sukses Makmur',
            'phone' => '081234567890',
            'address' => 'Jl. Raya Bogor KM 31, Cimanggis, Depok',
        ]);

        $this->category = Category::create([
            'name' => 'Makanan Ringan & Berat',
        ]);

        $this->unitPcs = Unit::create(['name' => 'Pcs', 'symbol' => 'pcs']);
        $this->unitDus = Unit::create(['name' => 'Dus', 'symbol' => 'dus']);

        // Indomie with multi-unit conversion: 1 Dus = 40 Pcs
        $this->productIndomie = Product::create([
            'category_id' => $this->category->id,
            'base_unit_id' => $this->unitPcs->id,
            'name' => 'Indomie Goreng Special',
            'sku' => 'IND-GOR-001',
            'purchase_price' => 2800,
            'selling_price' => 3500,
            'current_stock' => 10,
            'minimum_stock' => 20,
            'is_active' => true,
        ]);

        ProductUnit::create([
            'product_id' => $this->productIndomie->id,
            'unit_id' => $this->unitDus->id,
            'conversion_factor' => 40,
        ]);

        // Teh Botol (single unit only)
        $this->productTeh = Product::create([
            'category_id' => $this->category->id,
            'base_unit_id' => $this->unitPcs->id,
            'name' => 'Teh Botol Sosro',
            'sku' => 'TEH-BOT-001',
            'purchase_price' => 4000,
            'selling_price' => 5000,
            'current_stock' => 5,
            'minimum_stock' => 15,
            'is_active' => true,
        ]);
    }

    public function test_kasir_can_view_purchases_index_and_create_page(): void
    {
        $response = $this->actingAs($this->kasir)->get(route('purchases.index'));
        $response->assertStatus(200);
        $response->assertSee('Riwayat Faktur Barang Masuk');
        $response->assertSee('Catat Barang Masuk');

        $responseCreate = $this->actingAs($this->kasir)->get(route('purchases.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Catat Barang Masuk (Pengadaan)');
        $responseCreate->assertSee('PT Indofood Sukses Makmur');
    }

    public function test_admin_cannot_access_purchases_create_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('purchases.create'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_purchases_index_as_monitoring(): void
    {
        $response = $this->actingAs($this->admin)->get(route('purchases.index'));
        $response->assertStatus(200);
        $response->assertSee('Mode Monitoring (Hanya Baca)');
        $response->assertDontSee('Catat Barang Masuk');
    }

    public function test_kasir_can_record_incoming_goods_with_base_unit(): void
    {
        $initialStock = $this->productTeh->current_stock; // 5

        $payload = [
            'supplier_id' => $this->supplier->id,
            'receipt_number' => 'NOTA-SOSRO-001',
            'purchase_date' => now()->format('Y-m-d H:i:s'),
            'notes' => 'Penerimaan teh botol sosro',
            'items' => [
                [
                    'product_id' => $this->productTeh->id,
                    'unit_id' => $this->unitPcs->id,
                    'quantity' => 20,
                    'purchase_price' => 4000,
                ]
            ]
        ];

        $response = $this->actingAs($this->kasir)->post(route('purchases.store'), $payload);

        $purchase = Purchase::where('receipt_number', 'NOTA-SOSRO-001')->first();
        $this->assertNotNull($purchase);
        $this->assertEquals(80000, (float) $purchase->total_amount);

        // Verify redirection to show
        $response->assertRedirect(route('purchases.show', $purchase));

        // Verify purchase item
        $this->assertDatabaseHas('purchase_items', [
            'purchase_id' => $purchase->id,
            'product_id' => $this->productTeh->id,
            'unit_id' => $this->unitPcs->id,
            'quantity' => 20,
            'conversion_factor' => 1,
            'quantity_in_base_unit' => 20,
            'purchase_price' => 4000,
            'subtotal' => 80000,
        ]);

        // Verify product stock increment: 5 + 20 = 25
        $this->productTeh->refresh();
        $this->assertEquals(25, (float) $this->productTeh->current_stock);

        // Verify StockMovement audit trail
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->productTeh->id,
            'user_id' => $this->kasir->id,
            'type' => 'IN',
            'reference_type' => 'Purchase',
            'reference_id' => $purchase->id,
            'quantity' => 20,
            'stock_before' => 5,
            'stock_after' => 25,
        ]);
    }

    public function test_kasir_can_record_incoming_goods_with_multi_unit_conversion(): void
    {
        $initialStock = $this->productIndomie->current_stock; // 10 Pcs

        // Buy 2 Dus Indomie @ Rp 110.000 / Dus (1 Dus = 40 Pcs)
        // Converted base units = 2 * 40 = 80 Pcs
        // Subtotal = 2 * 110.000 = 220.000
        $payload = [
            'supplier_id' => $this->supplier->id,
            'receipt_number' => 'BM-IND-001',
            'purchase_date' => now()->format('Y-m-d H:i:s'),
            'notes' => 'Pasokan 2 dus indomie goreng',
            'items' => [
                [
                    'product_id' => $this->productIndomie->id,
                    'unit_id' => $this->unitDus->id,
                    'quantity' => 2,
                    'purchase_price' => 110000,
                ]
            ]
        ];

        $response = $this->actingAs($this->kasir)->post(route('purchases.store'), $payload);

        $purchase = Purchase::where('receipt_number', 'BM-IND-001')->first();
        $this->assertNotNull($purchase);
        $this->assertEquals(220000, (float) $purchase->total_amount);

        // Verify purchase item has snapshot conversion_factor = 40
        $this->assertDatabaseHas('purchase_items', [
            'purchase_id' => $purchase->id,
            'product_id' => $this->productIndomie->id,
            'unit_id' => $this->unitDus->id,
            'quantity' => 2,
            'conversion_factor' => 40,
            'quantity_in_base_unit' => 80,
            'purchase_price' => 110000,
            'subtotal' => 220000,
        ]);

        // Stock increment: 10 + 80 = 90 Pcs
        $this->productIndomie->refresh();
        $this->assertEquals(90, (float) $this->productIndomie->current_stock);

        // Stock movement recorded
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->productIndomie->id,
            'user_id' => $this->kasir->id,
            'type' => 'IN',
            'reference_type' => 'Purchase',
            'reference_id' => $purchase->id,
            'quantity' => 80,
            'stock_before' => 10,
            'stock_after' => 90,
        ]);
    }

    public function test_admin_cannot_submit_purchase_transaction(): void
    {
        $payload = [
            'supplier_id' => $this->supplier->id,
            'receipt_number' => 'BM-HACK-001',
            'purchase_date' => now()->format('Y-m-d H:i:s'),
            'items' => [
                [
                    'product_id' => $this->productTeh->id,
                    'unit_id' => $this->unitPcs->id,
                    'quantity' => 10,
                    'purchase_price' => 4000,
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)->post(route('purchases.store'), $payload);
        $response->assertStatus(403);
    }

    public function test_purchase_requires_valid_items_and_supplier(): void
    {
        $response = $this->actingAs($this->kasir)->post(route('purchases.store'), [
            'supplier_id' => 999999, // invalid
            'purchase_date' => now()->format('Y-m-d H:i:s'),
            'items' => [], // empty
        ]);

        $response->assertSessionHasErrors(['supplier_id', 'items']);
    }
}
