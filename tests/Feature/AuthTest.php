<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function adminUser()
    {
        return User::create([
            'name' => 'Admin Test',
            'email' => 'admin@example.test',
            'password' => Hash::make('password123'),
            'level' => 'admin',
        ]);
    }

    protected function staffUser()
    {
        return User::create([
            'name' => 'Staff Test',
            'email' => 'staff@example.test',
            'password' => Hash::make('password123'),
            'level' => 'bendahara',
        ]);
    }

    /** @test */
    public function guest_diarahkan_ke_login_saat_membuka_modul_internal()
    {
        foreach (['/dashboard', '/transaksi', '/kategori', '/coa', '/laporan', '/target-capaians', '/users'] as $uri) {
            $this->get($uri)->assertRedirect(route('login'));
        }
    }

    /** @test */
    public function login_valid_berhasil_dan_session_diregenerasi()
    {
        $this->adminUser();

        $this->post(route('login.attempt'), [
            'email' => 'admin@example.test',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->get(route('dashboard'))->assertOk();
    }

    /** @test */
    public function login_invalid_gagal_dengan_pesan_generik()
    {
        $this->adminUser();

        $this->from(route('login'))
            ->post(route('login.attempt'), [
                'email' => 'admin@example.test',
                'password' => 'salah-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    /** @test */
    public function email_tidak_terdaftar_memberikan_pesan_yang_sama()
    {
        $this->from(route('login'))
            ->post(route('login.attempt'), [
                'email' => 'tidak-ada@example.test',
                'password' => 'password123',
            ])->assertRedirect(route('login'));

        $this->assertGuest();
    }

    /** @test */
    public function login_user_yang_sudah_masuk_diarahkan_ke_dashboard()
    {
        $user = $this->adminUser();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('dashboard'));
    }

    /** @test */
    public function rate_limit_login_bekerja_setelah_percobaan_berulang()
    {
        $this->adminUser();

        for ($i = 0; $i < 5; $i++) {
            $this->from(route('login'))
                ->post(route('login.attempt'), [
                    'email' => 'admin@example.test',
                    'password' => 'password-salah',
                ])->assertRedirect(route('login'));
        }

        $this->from(route('login'))
            ->post(route('login.attempt'), [
                'email' => 'admin@example.test',
                'password' => 'password123',
            ])->assertStatus(429);
    }

    /** @test */
    public function logout_mengakhiri_session()
    {
        $user = $this->adminUser();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    /** @test */
    public function non_admin_tidak_dapat_mengakses_endpoint_manajemen_user()
    {
        $staff = $this->staffUser();

        $this->actingAs($staff)
            ->get(route('users.index'))
            ->assertForbidden();

        $this->actingAs($staff)
            ->get(route('users.create'))
            ->assertForbidden();
    }

    /** @test */
    public function admin_dapat_mengakses_endpoint_manajemen_user()
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk();
    }
}