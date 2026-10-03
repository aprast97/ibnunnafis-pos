<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReportTest extends TestCase
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
            'name'     => 'Kasir Laporan',
            'username' => '11111',
            'password' => bcrypt('password'),
        ]);

        $this->admin = User::create([
            'role_id'  => $adminRole->id,
            'name'     => 'Admin Laporan',
            'username' => '22222',
            'password' => bcrypt('password'),
        ]);

        $cat  = Category::create(['name' => 'Kitab']);
        $unit = Unit::create(['name' => 'Pcs', 'symbol' => 'pcs']);

        $this->product = Product::create([
            'category_id'    => $cat->id,
            'base_unit_id'   => $unit->id,
            'name'           => 'Kitab Fiqih Santri',
            'sku'            => 'KTB999',
            'purchase_price' => 10000,
            'selling_price'  => 15000,
            'current_stock'  => 50,
            'minimum_stock'  => 5,
            'is_active'      => true,
        ]);

        // Buat dummy penjualan sukses: 2 pcs @ 15.000 = 30.000 (HPP = 20.000, Laba = 10.000)
        $sale = Sale::create([
            'user_id'            => $this->kasir->id,
            'transaction_number' => 'TRX-' . now()->format('Ymd') . '-0001',
            'transaction_date'   => now(),
            'total_amount'       => 30000,
            'payment_amount'     => 50000,
            'change_amount'      => 20000,
            'status'             => 'COMPLETED',
        ]);

        SaleItem::create([
            'sale_id'    => $sale->id,
            'product_id' => $this->product->id,
            'unit_id'    => $unit->id,
            'quantity'   => 2,
            'unit_price' => 15000,
            'subtotal'   => 30000,
        ]);
    }

    #[Test]
    public function both_kasir_and_admin_can_access_reports_index(): void
    {
        $this->actingAs($this->kasir)
            ->get(route('reports.index'))
            ->assertStatus(200)
            ->assertSee('Laporan Penjualan & Analitik Keuangan')
            ->assertSee('Kitab Fiqih Santri');

        $this->actingAs($this->admin)
            ->get(route('reports.index'))
            ->assertStatus(200)
            ->assertSee('Laporan Penjualan & Analitik Keuangan');
    }

    #[Test]
    public function reports_page_calculates_revenue_and_profit_accurately(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index'));
        $response->assertStatus(200);

        // Omzet harus Rp 30.000 dan Laba Kotor Rp 10.000
        $response->assertSee('30.000');
        $response->assertSee('10.000');
    }

    #[Test]
    public function both_roles_can_access_printable_report(): void
    {
        $this->actingAs($this->kasir)
            ->get(route('reports.print'))
            ->assertStatus(200)
            ->assertSee('REKAPITULASI LAPORAN PENJUALAN & KEUANGAN', false);

        $this->actingAs($this->admin)
            ->get(route('reports.print'))
            ->assertStatus(200)
            ->assertSee('REKAPITULASI LAPORAN PENJUALAN & KEUANGAN', false);
    }

    #[Test]
    public function reports_can_export_csv_download(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reports.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}