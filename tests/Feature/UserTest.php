<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
    public function file_foto_lebih_dari_2mb_ditolak()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('foto.jpg')->size(2500); // 2.5 MB

        $this->actingAs($this->admin)
            ->from(route('users.create'))
            ->post(route('users.store'), $this->userData(['foto' => $file]))
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors('foto');
    }

    /** @test */
    public function file_non_image_ditolak()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('dokumen.txt', 10, 'text/plain');

        $this->actingAs($this->admin)
            ->from(route('users.create'))
            ->post(route('users.store'), $this->userData(['foto' => $file]))
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors('foto');
    }

    /** @test */
    public function foto_diberi_nama_acak_dan_tersimpan_pada_disk_yang_benar()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('foto-asli-saya.jpg');

        $this->actingAs($this->admin)
            ->post(route('users.store'), $this->userData(['foto' => $file]))
            ->assertRedirect(route('users.index'));

        $user = User::where('email', 'staff@example.test')->first();
        $this->assertNotNull($user->foto);
        $this->assertNotSame('foto-asli-saya.jpg', $user->foto);

        Storage::disk('public')->assertExists('foto/' . $user->foto);
    }

    /** @test */
    public function ganti_foto_membersihkan_foto_lama()
    {
        Storage::fake('public');

        $fotoLama = 'foto_lama_' . uniqid() . '.jpg';
        Storage::disk('public')->put('foto/' . $fotoLama, 'content');

        $user = User::create([
            'name' => 'Staff',
            'email' => 'staff@example.test',
            'password' => Hash::make('password123'),
            'level' => 'bendahara',
            'foto' => $fotoLama,
        ]);

        $fotoBaru = UploadedFile::fake()->image('foto-baru.jpg');

        $this->actingAs($this->admin)
            ->put(route('users.update', $user), $this->userData(['foto' => $fotoBaru]))
            ->assertRedirect(route('users.index'));

        Storage::disk('public')->assertMissing('foto/' . $fotoLama);
        Storage::disk('public')->assertExists('foto/' . $user->fresh()->foto);
    }

    /** @test */
    public function hapus_user_membersihkan_file_miliknya()
    {
        Storage::fake('public');

        $foto = 'foto_owner_' . uniqid() . '.jpg';
        Storage::disk('public')->put('foto/' . $foto, 'content');

        $user = User::create([
            'name' => 'Staff',
            'email' => 'staff@example.test',
            'password' => Hash::make('password123'),
            'level' => 'bendahara',
            'foto' => $foto,
        ]);

        $this->actingAs($this->admin)
            ->delete(route('users.destroy', $user))
            ->assertRedirect(route('users.index'));

        $this->assertNull(User::find($user->id));
        Storage::disk('public')->assertMissing('foto/' . $foto);
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