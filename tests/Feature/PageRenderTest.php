<?php

namespace Tests\Feature;

use App\Models\Coa;
use App\Models\Kategori;
use App\Models\TargetCapaian;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PageRenderTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $bendahara;
    protected $kategori;
    protected $coa;
    protected $transaksi;
    protected $target;
    protected $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@example.test',
            'password' => Hash::make('password123'),
            'level' => 'admin',
        ]);

        $this->bendahara = User::create([
            'name' => 'Bendahara',
            'email' => 'bendahara@example.test',
            'password' => Hash::make('password123'),
            'level' => 'bendahara',
        ]);

        $this->kategori = Kategori::create(['kategori' => 'Iuran Kas']);
        $this->coa = Coa::create([
            'kode_coa' => '101',
            'nama_coa' => 'Kas',
            'jenis' => 'Aset',
            'saldo' => 0,
        ]);
        $this->transaksi = Transaksi::create([
            'tanggal' => date('Y-m-d'),
            'jenis' => 'pemasukan',
            'kategori_id' => $this->kategori->id,
            'coa_id' => $this->coa->id,
            'nominal' => 100000,
            'keterangan' => 'Transaksi test',
        ]);
        $this->target = TargetCapaian::create([
            'tahun' => date('Y'),
            'target_capaian' => 5000000,
        ]);
        $this->staff = User::create([
            'name' => 'Staff',
            'email' => 'staff@example.test',
            'password' => Hash::make('password123'),
            'level' => 'bendahara',
        ]);
    }

    /** @test */
    public function admin_dapat_mengakses_semua_halaman_index_dan_form()
    {
        $pages = [
            ['dashboard', []],
            ['transaksi.index', []],
            ['transaksi.create', []],
            ['transaksi.edit', ['transaksi' => $this->transaksi->id]],
            ['kategori.index', []],
            ['kategori.create', []],
            ['kategori.edit', ['kategori' => $this->kategori->id]],
            ['coa.index', []],
            ['coa.create', []],
            ['coa.edit', ['coa' => $this->coa->id]],
            ['laporan.index', []],
            ['laporan.print', []],
            ['target-capaians.index', []],
            ['target-capaians.create', []],
            ['target-capaians.edit', ['target_capaian' => $this->target->id]],
            ['users.index', []],
            ['users.create', []],
            ['users.edit', ['user' => $this->staff->id]],
        ];

        foreach ($pages as [$route, $params]) {
            $this->actingAs($this->admin)
                ->get(route($route, $params))
                ->assertOk();
        }
    }

    /** @test */
    public function bendahara_dapat_mengakses_halaman_operasional()
    {
        $pages = [
            ['dashboard', []],
            ['transaksi.index', []],
            ['transaksi.create', []],
            ['transaksi.edit', ['transaksi' => $this->transaksi->id]],
            ['kategori.index', []],
            ['kategori.create', []],
            ['kategori.edit', ['kategori' => $this->kategori->id]],
            ['coa.index', []],
            ['coa.create', []],
            ['coa.edit', ['coa' => $this->coa->id]],
            ['laporan.index', []],
            ['laporan.print', []],
            ['target-capaians.index', []],
            ['target-capaians.create', []],
            ['target-capaians.edit', ['target_capaian' => $this->target->id]],
        ];

        foreach ($pages as [$route, $params]) {
            $this->actingAs($this->bendahara)
                ->get(route($route, $params))
                ->assertOk();
        }
    }

    /** @test */
    public function bendahara_tidak_dapat_mengakses_halaman_manajemen_user()
    {
        $this->actingAs($this->bendahara)
            ->get(route('users.index'))
            ->assertForbidden();

        $this->actingAs($this->bendahara)
            ->get(route('users.create'))
            ->assertForbidden();

        $this->actingAs($this->bendahara)
            ->get(route('users.edit', $this->staff))
            ->assertForbidden();
    }

    /** @test */
    public function laporan_dengan_filter_menampilkan_hasil()
    {
        $this->actingAs($this->bendahara)
            ->get(route('laporan.index', ['kategori_id' => $this->kategori->id]))
            ->assertOk()
            ->assertSee('Iuran Kas');
    }

    /** @test */
    public function dashboard_admin_menampilkan_konten_utama()
    {
        $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Aksi Cepat')
            ->assertSee('Transaksi Terbaru');
    }

    /** @test */
    public function tahun_invalid_tidak_masuk_daftar_filter_dashboard()
    {
        Transaksi::create([
            'tanggal' => '2224-05-10',
            'jenis' => 'pemasukan',
            'kategori_id' => $this->kategori->id,
            'coa_id' => $this->coa->id,
            'nominal' => 1000,
            'keterangan' => 'Data tahun rusak',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertOk();

        $tahunTersedia = $response->viewData('tahunTersedia');
        $this->assertNotContains(2224, $tahunTersedia->all());
        $this->assertContains((int) date('Y'), $tahunTersedia->all());

        // Filter tahun (pills) tidak boleh memuat 2224 — meski daftar transaksi terbaru tetap menampilkannya
        $response->assertDontSee('?year=2224');
    }

    /** @test */
    public function dashboard_tahun_kosong_menampilkan_notice_lompat_data()
    {
        // Data hanya ada di 2024, tahun berjalan (mis. 2026) kosong
        Transaksi::where('id', $this->transaksi->id)->update(['tanggal' => '2024-06-15']);

        $response = $this->actingAs($this->admin)
            ->get(route('dashboard', ['year' => date('Y')]))
            ->assertOk();

        $this->assertFalse($response->viewData('selectedHasData'));
        $this->assertEquals(2024, $response->viewData('latestDataYear'));
        $response->assertSee('Belum ada transaksi di tahun ' . date('Y'));
    }
}