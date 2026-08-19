<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('password123'),
            'level' => 'admin',
        ]);
    }

    protected function userData(array $overrides = [])
    {
        return array_merge([
            'name' => 'Staff Baru',
            'email' => 'staff@example.test',
            'password' => 'password123',
            'level' => 'bendahara',
        ], $overrides);
    }

    /** @test */
    public function email_duplikat_ditolak()
    {
        User::create([
            'name' => 'Lama',
            'email' => 'sama@example.test',
            'password' => Hash::make('password123'),
            'level' => 'bendahara',
        ]);

        $this->actingAs($this->admin)
            ->from(route('users.create'))
            ->post(route('users.store'), $this->userData(['email' => 'sama@example.test']))
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function password_tersimpan_sebagai_hash()
    {
        $this->actingAs($this->admin)
            ->post(route('users.store'), $this->userData())
            ->assertRedirect(route('users.index'));

        $user = User::where('email', 'staff@example.test')->first();
        $this->assertNotNull($user);
        $this->assertNotSame('password123', $user->password);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /** @test */
    public function hapus_user_berhasil()
    {
        $user = User::create([
            'name' => 'Staff',
            'email' => 'staff@example.test',
            'password' => Hash::make('password123'),
            'level' => 'bendahara',
        ]);

        $this->actingAs($this->admin)
            ->delete(route('users.destroy', $user))
            ->assertRedirect(route('users.index'));

        $this->assertNull(User::find($user->id));
    }

    /** @test */
    public function user_tidak_dapat_menghapus_dirinya_sendiri()
    {
        $this->actingAs($this->admin)
            ->from(route('users.index'))
            ->delete(route('users.destroy', $this->admin))
            ->assertRedirect(route('users.index'));

        $this->assertNotNull(User::find($this->admin->id));
    }

    /** @test */
    public function administrator_terakhir_tidak_dapat_dihapus()
    {
        $staff = User::create([
            'name' => 'Staff',
            'email' => 'staff@example.test',
            'password' => Hash::make('password123'),
            'level' => 'bendahara',
        ]);

        $this->actingAs($this->admin)
            ->from(route('users.index'))
            ->delete(route('users.destroy', $this->admin))
            ->assertRedirect(route('users.index'));

        $this->assertNotNull(User::find($this->admin->id));
        $this->assertNotNull(User::find($staff->id));
    }

    /** @test */
    public function password_kosong_saat_edit_berarti_password_lama_dipertahankan()
    {
        $user = User::create([
            'name' => 'Staff',
            'email' => 'staff@example.test',
            'password' => Hash::make('password123'),
            'level' => 'bendahara',
        ]);

        $passwordLama = $user->password;

        $this->actingAs($this->admin)
            ->put(route('users.update', $user), $this->userData([
                'email' => 'staff@example.test',
                'password' => '',
                'level' => 'bendahara',
            ]))
            ->assertRedirect(route('users.index'));

        $this->assertSame($passwordLama, $user->fresh()->password);
    }

    /** @test */
    public function email_tetap_unik_dengan_mengecualikan_user_yang_diedit()
    {
        $user = User::create([
            'name' => 'Staff',
            'email' => 'staff@example.test',
            'password' => Hash::make('password123'),
            'level' => 'bendahara',
        ]);

        $this->actingAs($this->admin)
            ->put(route('users.update', $user), $this->userData([
                'email' => 'staff@example.test',
                'password' => '',
                'level' => 'bendahara',
            ]))
            ->assertRedirect(route('users.index'));
    }
}