<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of system users (Admin only).
     */
    public function index(Request $request): View
    {
        $search = trim($request->get('search', ''));
        $roleId = $request->get('role_id');

        $query = User::with('role')
            ->withCount(['sales', 'purchases', 'stockOpnames', 'stockMovements']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleId && $roleId !== 'all') {
            $query->where('role_id', $roleId);
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        // Metrics
        $totalUsers = User::count();
        $totalKasir = User::whereHas('role', fn($q) => $q->where('name', 'Kasir'))->count();
        $totalAdmin = User::whereHas('role', fn($q) => $q->where('name', 'Admin'))->count();

        $roles = Role::orderBy('name')->get();

        return view('users.index', compact(
            'users',
            'roles',
            'search',
            'roleId',
            'totalUsers',
            'totalKasir',
            'totalAdmin'
        ));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'numeric', 'digits:5', 'unique:users,username'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'string', 'min:5'],
        ], [
            'username.digits' => 'ID Pengguna / Username harus berupa 5 digit angka (contoh: 52194).',
            'username.numeric' => 'ID Pengguna harus berupa angka.',
            'username.unique' => 'ID Pengguna / Username ini sudah terdaftar.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'password.min' => 'Kata sandi minimal 5 karakter.',
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.index')->with('success', "Pengguna baru '{$validated['name']}' (ID: {$validated['username']}) berhasil didaftarkan ke dalam sistem.");
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'numeric', 'digits:5', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'string', 'min:5'],
        ], [
            'username.digits' => 'ID Pengguna / Username harus berupa 5 digit angka (contoh: 52194).',
            'username.numeric' => 'ID Pengguna harus berupa angka.',
            'username.unique' => 'ID Pengguna / Username ini sudah terdaftar.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'password.min' => 'Kata sandi minimal 5 karakter.',
        ]);

        // Safety Guard: Cannot remove the last administrator
        if ($user->isAdmin() && (int)$validated['role_id'] !== $user->role_id) {
            $adminCount = User::whereHas('role', fn($q) => $q->where('name', 'Admin'))->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Tidak dapat mengubah peran: Harus ada minimal satu Administrator aktif di sistem.');
            }
        }

        $userData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        return redirect()->route('users.index')->with('success', "Data pengguna '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // 1. Guard against self-deletion
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.');
        }

        // 2. Guard against deleting the last administrator
        if ($user->isAdmin()) {
            $adminCount = User::whereHas('role', fn($q) => $q->where('name', 'Admin'))->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Tidak dapat menghapus Administrator terakhir dalam sistem.');
            }
        }

        // 3. Guard against deleting users with recorded activity (audit integrity)
        $hasActivity = $user->sales()->exists()
            || $user->purchases()->exists()
            || $user->stockOpnames()->exists()
            || $user->stockMovements()->exists();

        if ($hasActivity) {
            return back()->with('error', "Pengguna '{$user->name}' tidak dapat dihapus karena memiliki riwayat transaksi/aktivitas persediaan di sistem. Akun ini diperlukan untuk integritas audit.");
        }

        $deletedName = $user->name;
        $user->delete();

        return redirect()->route('users.index')->with('success', "Akun pengguna '{$deletedName}' berhasil dihapus dari sistem.");
    }
}