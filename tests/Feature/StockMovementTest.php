<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    protected User $kasir;
    protected User $admin;
    protected Product $product;

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

        $category = Category::create(['name' => 'Minuman']);
        $unit = Unit::create(['name' => 'Botol', 'symbol' => 'btl']);

        $this->product = Product::create([
            'category_id' => $category->id,
            'base_unit_id' => $unit->id,
            'name' => 'Golda Coffee 200ml',
            'sku' => 'GOL-COF-001',
            'purchase_price' => 2500,
            'selling_price' => 3500,
            'current_stock' => 50,
            'minimum_stock' => 10,
            'is_active' => true,
        ]);

        // Create sample movements
        StockMovement::create([
            'product_id' => $this->product->id,
            'user_id' => $this->kasir->id,
            'type' => 'IN',
            'reference_type' => 'Purchase',
            'reference_id' => 1,
            'quantity' => 50,
            'stock_before' => 0,
            'stock_after' => 50,
            'notes' => 'Penerimaan stok pertama',
            'created_at' => now(),
        ]);
    }

    public function test_kasir_and_admin_can_view_stock_movements_index(): void
    {
        $responseKasir = $this->actingAs($this->kasir)->get(route('stock-movements.index'));
        $responseKasir->assertStatus(200);
        $responseKasir->assertSee('Kartu Stok &amp; Riwayat Mutasi', false);
        $responseKasir->assertSee('Golda Coffee 200ml');

        $responseAdmin = $this->actingAs($this->admin)->get(route('stock-movements.index'));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Kartu Stok &amp; Riwayat Mutasi', false);
        $responseAdmin->assertSee('Golda Coffee 200ml');
    }

    public function test_can_filter_stock_movements_by_product(): void
    {
        $response = $this->actingAs($this->kasir)->get(route('stock-movements.index', [
            'product_id' => $this->product->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Kartu Stok Produk: Golda Coffee 200ml');
        $response->assertSee('+50 Botol');
    }

    public function test_can_filter_stock_movements_by_type(): void
    {
        $response = $this->actingAs($this->kasir)->get(route('stock-movements.index', [
            'type' => 'IN',
        ]));

        $response->assertStatus(200);
        $response->assertSee('MASUK (IN)');
    }
}
