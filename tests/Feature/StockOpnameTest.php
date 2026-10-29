<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\StockOpname;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StockOpnameTest extends TestCase
{
    use RefreshDatabase;

    private User $kasir;

    private User $admin;

    private Product $product1;

    private Product $product2;

    protected function setUp(): void
    {
        parent::setUp();

        $kasirRole = Role::create(['name' => 'Kasir']);
        $adminRole = Role::create(['name' => 'Admin']);

        $this->kasir = User::create([
            'role_id' => $kasirRole->id,
            'name' => 'Kasir Test',
            'username' => '11111',
            'password' => bcrypt('password'),
        ]);

        $this->admin = User::create([
            'role_id' => $adminRole->id,
            'name' => 'Admin Test',
            'username' => '22222',
            'password' => bcrypt('password'),
        ]);

        $cat = Category::create(['name' => 'Kitab']);
        $unit = Unit::create(['name' => 'Pcs', 'symbol' => 'pcs']);

        $this->product1 = Product::create([
            'category_id' => $cat->id,
            'base_unit_id' => $unit->id,
            'name' => 'Kitab Fiqih 1',
            'sku' => 'KTB001',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'current_stock' => 20,
            'minimum_stock' => 5,
            'is_active' => true,
        ]);

        $this->product2 = Product::create([
            'category_id' => $cat->id,
            'base_unit_id' => $unit->id,
            'name' => 'Kitab Tajwid',
            'sku' => 'KTB002',
            'purchase_price' => 8000,
            'selling_price' => 12000,
            'current_stock' => 30,
            'minimum_stock' => 5,
            'is_active' => true,
        ]);
    }

    #[Test]
    public function kasir_can_view_stock_opname_index_and_create_page(): void
    {
        $this->actingAs($this->kasir)
            ->get(route('stock-opname.index'))
            ->assertStatus(200)
            ->assertSee('Stock Opname & Audit Fisik');

        $this->actingAs($this->kasir)
            ->get(route('stock-opname.create'))
            ->assertStatus(200)
            ->assertSee('Formulir Pemeriksaan Fisik Stok')
            ->assertSee('Kitab Fiqih 1');
    }

    #[Test]
    public function admin_cannot_access_stock_opname_create_page(): void
    {
        $this->actingAs($this->admin)
            ->get(route('stock-opname.create'))
            ->assertStatus(403);
    }

    #[Test]
    public function admin_can_view_stock_opname_index_and_details(): void
    {
        $opname = StockOpname::create([
            'user_id' => $this->kasir->id,
            'opname_date' => now(),
            'status' => 'COMPLETED',
            'notes' => 'Audit Akhir Bulan',
        ]);

        $this->actingAs($this->admin)
            ->get(route('stock-opname.index'))
            ->assertStatus(200)
            ->assertSee('Audit Akhir Bulan');

        $this->actingAs($this->admin)
            ->get(route('stock-opname.show', $opname))
            ->assertStatus(200);
    }

    #[Test]
    public function kasir_can_submit_stock_opname_and_adjust_stock_when_difference_exists(): void
    {
        $initialStock1 = $this->product1->current_stock; // 20
        $initialStock2 = $this->product2->current_stock; // 30

        // Misal hasil audit fisik:
        // Product1 fisik ada 18 (selisih -2, hilang/rusak)
        // Product2 fisik ada 35 (selisih +5, kelebihan fisik)
        $response = $this->actingAs($this->kasir)->post(route('stock-opname.store'), [
            'opname_date' => now()->format('Y-m-d H:i:s'),
            'notes' => 'Pemeriksaan rak santri',
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'physical_stock' => 18,
                    'notes' => '2 pcs rusak kena air',
                ],
                [
                    'product_id' => $this->product2->id,
                    'physical_stock' => 35,
                    'notes' => 'Kelebihan kiriman supplier',
                ],
            ],
        ]);

        $opname = StockOpname::first();
        $response->assertRedirect(route('stock-opname.show', $opname));

        // Stok di tabel products harus ter-update sesuai fisik
        $this->assertEquals(18, $this->product1->fresh()->current_stock);
        $this->assertEquals(35, $this->product2->fresh()->current_stock);

        // Record StockOpnameItem tersimpan dengan selisih yang tepat
        $this->assertDatabaseHas('stock_opname_items', [
            'stock_opname_id' => $opname->id,
            'product_id' => $this->product1->id,
            'system_stock' => 20,
            'physical_stock' => 18,
            'difference' => -2,
        ]);

        $this->assertDatabaseHas('stock_opname_items', [
            'stock_opname_id' => $opname->id,
            'product_id' => $this->product2->id,
            'system_stock' => 30,
            'physical_stock' => 35,
            'difference' => 5,
        ]);

        // StockMovement tercatat bertipe ADJUSTMENT
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product1->id,
            'type' => 'ADJUSTMENT',
            'quantity' => -2,
            'stock_before' => 20,
            'stock_after' => 18,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product2->id,
            'type' => 'ADJUSTMENT',
            'quantity' => 5,
            'stock_before' => 30,
            'stock_after' => 35,
        ]);
    }

    #[Test]
    public function stock_is_not_altered_when_difference_is_zero(): void
    {
        $response = $this->actingAs($this->kasir)->post(route('stock-opname.store'), [
            'opname_date' => now()->format('Y-m-d H:i:s'),
            'notes' => 'Pemeriksaan cocok semua',
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'physical_stock' => 20, // sama persis dengan sistem
                    'notes' => 'Stok klop',
                ],
            ],
        ]);

        $opname = StockOpname::first();
        $response->assertRedirect(route('stock-opname.show', $opname));

        // Stok tidak berubah
        $this->assertEquals(20, $this->product1->fresh()->current_stock);

        // Tidak ada StockMovement yang dibuat karena selisih 0
        $this->assertDatabaseMissing('stock_movements', [
            'reference_type' => StockOpname::class,
            'reference_id' => $opname->id,
        ]);
    }

    #[Test]
    public function damaged_stock_can_be_adjusted_down_to_zero(): void
    {
        $response = $this->actingAs($this->kasir)->post(route('stock-opname.store'), [
            'opname_date' => now()->format('Y-m-d H:i:s'),
            'notes' => 'Pemeriksaan barang rusak',
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'physical_stock' => 0,
                    'notes' => 'Seluruh stok rusak',
                ],
            ],
        ]);

        $opname = StockOpname::first();
        $response->assertRedirect(route('stock-opname.show', $opname));

        $this->assertEquals(0, $this->product1->fresh()->current_stock);
        $this->assertDatabaseHas('stock_opname_items', [
            'stock_opname_id' => $opname->id,
            'product_id' => $this->product1->id,
            'system_stock' => 20,
            'physical_stock' => 0,
            'difference' => -20,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product1->id,
            'type' => 'ADJUSTMENT',
            'quantity' => -20,
            'stock_before' => 20,
            'stock_after' => 0,
        ]);
    }
}
