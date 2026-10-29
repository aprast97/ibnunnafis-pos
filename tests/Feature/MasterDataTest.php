<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected User $kasir;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->kasir = User::where('username', '39147')->first(); // Abila (Kasir)
        $this->admin = User::where('username', '84921')->first(); // Ummi Romlah (Admin)
    }

    public function test_kasir_can_view_and_create_category(): void
    {
        $response = $this->actingAs($this->kasir)->get('/categories');
        $response->assertStatus(200);
        $response->assertSee('Kategori Produk');

        $postResponse = $this->actingAs($this->kasir)->post('/categories', [
            'name' => 'Kategori Baru Percobaan',
            'description' => 'Deskripsi percobaan',
        ]);

        $postResponse->assertRedirect('/categories');
        $postResponse->assertSessionHas('success');
        $this->assertDatabaseHas('categories', ['name' => 'Kategori Baru Percobaan']);
    }

    public function test_category_deletion_blocked_if_products_exist(): void
    {
        $categoryWithProducts = Category::whereHas('products')->first();
        $this->assertNotNull($categoryWithProducts);

        $response = $this->actingAs($this->kasir)->delete("/categories/{$categoryWithProducts->id}");
        $response->assertRedirect('/categories');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $categoryWithProducts->id]);
    }

    public function test_kasir_can_create_unit(): void
    {
        $response = $this->actingAs($this->kasir)->post('/units', [
            'name' => 'Renceng',
            'symbol' => 'rcg',
        ]);

        $response->assertRedirect('/units');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('units', ['name' => 'Renceng', 'symbol' => 'rcg']);
    }

    public function test_unit_deletion_blocked_if_used(): void
    {
        $unitUsed = Unit::whereHas('products')->first();
        $this->assertNotNull($unitUsed);

        $response = $this->actingAs($this->kasir)->delete("/units/{$unitUsed->id}");
        $response->assertRedirect('/units');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('units', ['id' => $unitUsed->id]);
    }

    public function test_kasir_can_create_and_update_supplier(): void
    {
        $response = $this->actingAs($this->kasir)->post('/suppliers', [
            'name' => 'Distributor Joyday Depok',
            'phone' => '081299887766',
            'address' => 'Jl. Margonda Raya No. 99',
        ]);

        $response->assertRedirect('/suppliers');
        $this->assertDatabaseHas('suppliers', ['name' => 'Distributor Joyday Depok']);

        $supplier = Supplier::where('name', 'Distributor Joyday Depok')->first();

        $updateResponse = $this->actingAs($this->kasir)->put("/suppliers/{$supplier->id}", [
            'name' => 'Distributor Joyday & Aice Depok',
            'phone' => '081299887766',
            'address' => 'Jl. Margonda Raya No. 99',
        ]);

        $updateResponse->assertRedirect('/suppliers');
        $this->assertDatabaseHas('suppliers', ['name' => 'Distributor Joyday & Aice Depok']);
    }

    public function test_kasir_can_create_product_with_multi_unit_conversion(): void
    {
        $category = Category::first();
        $unitPcs = Unit::where('name', 'Pcs')->first();
        $unitDus = Unit::where('name', 'Dus')->first();

        $response = $this->actingAs($this->kasir)->post('/products', [
            'name' => 'Kopi Good Day Cappuccino Sachet',
            'sku' => '899990901',
            'category_id' => $category->id,
            'base_unit_id' => $unitPcs->id,
            'purchase_price' => 1800,
            'selling_price' => 2500,
            'current_stock' => 50,
            'minimum_stock' => 10,
            'conversion_unit_id' => $unitDus->id,
            'conversion_factor' => 30, // 1 Dus = 30 Pcs
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', [
            'name' => 'Kopi Good Day Cappuccino Sachet',
            'sku' => '899990901',
            'current_stock' => 50,
        ]);

        $product = Product::where('sku', '899990901')->first();
        $this->assertNotNull($product);

        // Verify conversion unit created
        $this->assertDatabaseHas('product_units', [
            'product_id' => $product->id,
            'unit_id' => $unitDus->id,
            'conversion_factor' => 30,
        ]);

        // Verify initial stock movement recorded
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'IN',
            'reference_type' => 'InitialStock',
            'quantity' => 50,
        ]);
    }

    public function test_kasir_can_toggle_product_active_status(): void
    {
        $product = Product::first();
        $this->assertTrue($product->is_active);

        $response = $this->actingAs($this->kasir)->patch("/products/{$product->id}/toggle-status");
        $response->assertRedirect('/products');

        $product->refresh();
        $this->assertFalse($product->is_active);
    }

    public function test_admin_cannot_mutate_master_data_returns_403(): void
    {
        // Admin attempts to create category
        $response = $this->actingAs($this->admin)->post('/categories', [
            'name' => 'Kategori Ilegal Admin',
        ]);
        $response->assertStatus(403);

        // Admin attempts to create product
        $response = $this->actingAs($this->admin)->post('/products', [
            'name' => 'Produk Ilegal Admin',
            'category_id' => 1,
            'base_unit_id' => 1,
            'purchase_price' => 1000,
            'selling_price' => 1500,
            'minimum_stock' => 5,
        ]);
        $response->assertStatus(403);

        // Admin attempts to create supplier
        $response = $this->actingAs($this->admin)->post('/suppliers', [
            'name' => 'Supplier Ilegal Admin',
        ]);
        $response->assertStatus(403);
    }

    public function test_admin_can_view_products_and_details_read_only(): void
    {
        $response = $this->actingAs($this->admin)->get('/products');
        $response->assertStatus(200);
        $response->assertSee('Mode Monitoring (Hanya Baca)');
        $response->assertDontSee('Tambah Produk Baru');

        $product = Product::first();
        $detailResponse = $this->actingAs($this->admin)->get("/products/{$product->id}");
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee($product->name);
        $detailResponse->assertDontSee('Edit Produk');
    }

    public function test_product_mutations_keep_the_current_catalog_page(): void
    {
        $category = Category::first();
        $unit = Unit::first();

        for ($index = 1; $index <= 16; $index++) {
            Product::create([
                'category_id' => $category->id,
                'base_unit_id' => $unit->id,
                'name' => "Produk Pagination {$index}",
                'purchase_price' => 1000,
                'selling_price' => 1500,
                'current_stock' => 10,
                'minimum_stock' => 5,
                'is_active' => true,
            ]);
        }

        $product = Product::orderBy('name')->skip(15)->first();
        $this->assertNotNull($product);

        $pageResponse = $this->actingAs($this->kasir)
            ->get(route('products.index', ['page' => 2]));

        $pageResponse->assertStatus(200)
            ->assertSee(route('products.edit', [
                'product' => $product,
                'page' => 2,
            ]), false);

        $updateResponse = $this->actingAs($this->kasir)
            ->put(route('products.update', $product), [
                'name' => $product->name . ' Updated',
                'category_id' => $category->id,
                'base_unit_id' => $unit->id,
                'purchase_price' => 1000,
                'selling_price' => 1500,
                'minimum_stock' => 5,
                'return_query' => ['page' => 2],
            ]);

        $updateResponse->assertRedirect(route('products.index', ['page' => 2]));
    }
}
