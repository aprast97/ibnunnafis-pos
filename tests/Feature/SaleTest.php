<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    private User $kasir;
    private User $admin;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $kasirRole = Role::create(['name' => 'Kasir']);
        $adminRole = Role::create(['name' => 'Admin']);

        $this->kasir = User::create([
            'role_id'  => $kasirRole->id,
            'name'     => 'Kasir Test',
            'username' => '11111',
            'email'    => 'kasir@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->admin = User::create([
            'role_id'  => $adminRole->id,
            'name'     => 'Admin Test',
            'username' => '22222',
            'email'    => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $cat  = Category::create(['name' => 'Makanan']);
        $unit = Unit::create(['name' => 'Pcs', 'symbol' => 'pcs']);

        $this->product = Product::create([
            'category_id'    => $cat->id,
            'base_unit_id'   => $unit->id,
            'name'           => 'Indomie Goreng',
            'sku'            => 'MIE001',
            'purchase_price' => 2500,
            'selling_price'  => 3500,
            'current_stock'  => 50,
            'minimum_stock'  => 10,
            'is_active'      => true,
        ]);
    }

    #[Test]
    public function kasir_can_view_pos_page(): void
    {
        $response = $this->actingAs($this->kasir)->get(route('pos.index'));
        $response->assertStatus(200);
        $response->assertSee('Kasir & Transaksi Penjualan');
        $response->assertSee('Indomie Goreng');
    }

    #[Test]
    public function admin_cannot_access_pos(): void
    {
        $response = $this->actingAs($this->admin)->get(route('pos.index'));
        $response->assertStatus(403);
    }

    #[Test]
    public function kasir_can_process_sale_and_stock_is_deducted(): void
    {
        $initialStock = $this->product->current_stock; // 50

        $response = $this->actingAs($this->kasir)->postJson(route('pos.store'), [
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity'   => 3,
                    'unit_price' => 3500,
                    'subtotal'   => 10500,
                ],
            ],
            'total_amount'   => 10500,
            'payment_amount' => 20000,
            'change_amount'  => 9500,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals($initialStock - 3, $this->product->fresh()->current_stock);

        $this->assertDatabaseHas('sales', [
            'user_id'        => $this->kasir->id,
            'total_amount'   => 10500,
            'payment_amount' => 20000,
            'change_amount'  => 9500,
            'status'         => 'COMPLETED',
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type'       => 'OUT',
            'quantity'   => -3,
        ]);
    }

    #[Test]
    public function sale_fails_when_stock_insufficient(): void
    {
        $response = $this->actingAs($this->kasir)->postJson(route('pos.store'), [
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity'   => 999,
                    'unit_price' => 3500,
                    'subtotal'   => 3496500,
                ],
            ],
            'total_amount'   => 3496500,
            'payment_amount' => 4000000,
            'change_amount'  => 503500,
        ]);

        $response->assertStatus(422);
        $this->assertEquals(50, $this->product->fresh()->current_stock);
    }

    #[Test]
    public function both_kasir_and_admin_can_view_transaction_history(): void
    {
        $this->actingAs($this->kasir)->get(route('transactions.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('transactions.index'))->assertStatus(200);
    }

    #[Test]
    public function transaction_number_auto_generated_with_correct_format(): void
    {
        $number = Sale::generateTransactionNumber();
        $this->assertMatchesRegularExpression('/^TRX-\d{8}-\d{4}$/', $number);
    }

    #[Test]
    public function admin_can_cancel_sale_and_stock_is_restored(): void
    {
        $this->actingAs($this->kasir)->postJson(route('pos.store'), [
            'items' => [[
                'product_id' => $this->product->id,
                'quantity'   => 5,
                'unit_price' => 3500,
                'subtotal'   => 17500,
            ]],
            'total_amount'   => 17500,
            'payment_amount' => 20000,
            'change_amount'  => 2500,
        ]);

        $sale = Sale::first();

        $response = $this->actingAs($this->admin)->patch(route('transactions.cancel', $sale), [
            'cancellation_reason' => 'Produk rusak / salah hitung kasir',
        ]);

        $response->assertRedirect(route('transactions.index'));
        $this->assertEquals(50, $this->product->fresh()->current_stock);
        $this->assertEquals('CANCELLED', $sale->fresh()->status);
    }
}