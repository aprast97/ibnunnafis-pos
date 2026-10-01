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
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $kasir;
    private Role $adminRole;
    private Role $kasirRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kasirRole = Role::create([
            'name' => 'Kasir',
            'description' => 'Petugas Kasir & Operasional Harian Kantin',
        ]);

        $this->adminRole = Role::create([
            'name' => 'Admin',
            'description' => 'Monitoring Operasional & Laporan Kantin',
        ]);

        $this->admin = User::create([
            'role_id' => $this->adminRole->id,
            'name' => 'Ummi Hj. Romlah',
            'username' => '84921',
            'email' => 'admin@ibnunnafis.com',
            'password' => Hash::make('84921'),
        ]);

        $this->kasir = User::create([
            'role_id' => $this->kasirRole->id,
            'name' => 'Raudhatul Abila',
            'username' => '39147',
            'email' => 'kasir@ibnunnafis.com',
            'password' => Hash::make('39147'),
        ]);
    }

    #[Test]
    public function guest_cannot_access_user_management(): void
    {
        $response = $this->get(route('users.index'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function kasir_cannot_access_user_management(): void
    {
        $response = $this->actingAs($this->kasir)->get(route('users.index'));
        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_view_user_management(): void
    {
        $response = $this->actingAs($this->admin)->get(route('users.index'));
        $response->assertStatus(200);
        $response->assertSee('Manajemen Pengguna &amp; Hak Akses', false);
        $response->assertSee('Ummi Hj. Romlah');
        $response->assertSee('Raudhatul Abila');
        $response->assertSee('84921');
        $response->assertSee('39147');
    }

    #[Test]
    public function admin_can_create_new_kasir_with_5_digit_id(): void
    {
        $response = $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'Alya Nur Fadhilah',
            'username' => '72583',
            'email' => 'alya@ibnunnafis.com',
            'role_id' => $this->kasirRole->id,
            'password' => '72583',
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Alya Nur Fadhilah',
            'username' => '72583',
            'email' => 'alya@ibnunnafis.com',
            'role_id' => $this->kasirRole->id,
        ]);

        $newUser = User::where('username', '72583')->first();
        $this->assertTrue(Hash::check('72583', $newUser->password));
    }

    #[Test]
    public function username_must_be_exactly_5_digits(): void
    {
        // 4 digits should fail
        $response1 = $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'Invalid User 4 Digits',
            'username' => '1234',
            'email' => 'invalid4@test.com',
            'role_id' => $this->kasirRole->id,
            'password' => '12345',
        ]);
        $response1->assertSessionHasErrors('username');

        // 6 digits should fail
        $response2 = $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'Invalid User 6 Digits',
            'username' => '123456',
            'email' => 'invalid6@test.com',
            'role_id' => $this->kasirRole->id,
            'password' => '12345',
        ]);
        $response2->assertSessionHasErrors('username');

        // Non-numeric should fail
        $response3 = $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'Invalid User Alphabet',
            'username' => 'abcde',
            'email' => 'invalidabc@test.com',
            'role_id' => $this->kasirRole->id,
            'password' => '12345',
        ]);
        $response3->assertSessionHasErrors('username');
    }

    #[Test]
    public function admin_can_update_user_details(): void
    {
        $response = $this->actingAs($this->admin)->put(route('users.update', $this->kasir), [
            'name' => 'Raudhatul Abila Updated',
            'username' => '39147',
            'email' => 'kasir.updated@ibnunnafis.com',
            'role_id' => $this->kasirRole->id,
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $this->kasir->id,
            'name' => 'Raudhatul Abila Updated',
            'email' => 'kasir.updated@ibnunnafis.com',
        ]);
    }

    #[Test]
    public function admin_can_update_user_password(): void
    {
        $response = $this->actingAs($this->admin)->put(route('users.update', $this->kasir), [
            'name' => $this->kasir->name,
            'username' => $this->kasir->username,
            'email' => $this->kasir->email,
            'role_id' => $this->kasir->role_id,
            'password' => 'newpassword123',
        ]);

        $response->assertRedirect(route('users.index'));

        $this->kasir->refresh();
        $this->assertTrue(Hash::check('newpassword123', $this->kasir->password));
    }

    #[Test]
    public function admin_cannot_demote_the_only_admin(): void
    {
        $response = $this->actingAs($this->admin)->put(route('users.update', $this->admin), [
            'name' => $this->admin->name,
            'username' => $this->admin->username,
            'email' => $this->admin->email,
            'role_id' => $this->kasirRole->id, // attempt demotion
        ]);

        $response->assertSessionHas('error');
        $this->admin->refresh();
        $this->assertTrue($this->admin->isAdmin());
    }

    #[Test]
    public function admin_cannot_delete_self(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('users.destroy', $this->admin));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    #[Test]
    public function admin_cannot_delete_last_admin(): void
    {
        // Even if requested through different session or call
        $secondAdmin = User::create([
            'role_id' => $this->adminRole->id,
            'name' => 'Second Admin',
            'username' => '99999',
            'email' => 'second@ibnunnafis.com',
            'password' => Hash::make('99999'),
        ]);

        // Delete first admin from second admin session works
        $this->actingAs($secondAdmin)->delete(route('users.destroy', $this->admin));
        $this->assertDatabaseMissing('users', ['id' => $this->admin->id]);

        // Now secondAdmin is the LAST admin; attempting to delete should fail
        $anotherKasir = $this->kasir;
        $response = $this->actingAs($secondAdmin)->delete(route('users.destroy', $secondAdmin));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $secondAdmin->id]);
    }

    #[Test]
    public function admin_cannot_delete_user_with_sales_history(): void
    {
        $cat = Category::create(['name' => 'Snack']);
        $unit = Unit::create(['name' => 'Pcs', 'symbol' => 'pcs']);
        $product = Product::create([
            'category_id' => $cat->id,
            'base_unit_id' => $unit->id,
            'name' => 'Keripik Tempe',
            'purchase_price' => 2000,
            'selling_price' => 3000,
            'current_stock' => 10,
            'minimum_stock' => 2,
        ]);

        // Kasir makes a sale
        $sale = Sale::create([
            'user_id' => $this->kasir->id,
            'transaction_number' => 'TRX-TEST-001',
            'transaction_date' => now(),
            'total_amount' => 3000,
            'payment_amount' => 5000,
            'change_amount' => 2000,
            'status' => 'COMPLETED',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('users.destroy', $this->kasir));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->kasir->id]);
    }

    #[Test]
    public function admin_can_delete_unused_user(): void
    {
        $newUser = User::create([
            'role_id' => $this->kasirRole->id,
            'name' => 'Kasir Magang',
            'username' => '55555',
            'email' => 'magang@ibnunnafis.com',
            'password' => Hash::make('55555'),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('users.destroy', $newUser));

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $newUser->id]);
    }
}