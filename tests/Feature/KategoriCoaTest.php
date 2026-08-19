<?php

namespace Tests\Feature;

use App\Models\Cluster;
use App\Models\Coa;
use App\Models\Kategori;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KategoriCoaTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Bendahara',
            'email' => 'bendahara@example.test',
            'password' => Hash::make('password123'),
            'level' => 'bendahara',
        ]);
    }

    protected function coaData(array $overrides = [])
    {
        return array_merge([
            'kode_coa' => '101',
            'nama_coa' => 'Kas',
            'jenis' => 'Aset',
            'cluster' => null,
            'saldo_awal' => '0',
        ], $overrides);
    }

    /** @test */
    public function crud_kategori_valid_berhasil()
    {
        // Create
        $this->actingAs($this->user)
            ->post(route('kategori.store'), ['kategori' => 'ATK'])
            ->assertRedirect(route('kategori.index'));

        $kategori = Kategori::where('kategori', 'ATK')->first();
        $this->assertNotNull($kategori);

        // Edit
        $this->actingAs($this->user)
            ->put(route('kategori.update', $kategori), ['kategori' => 'Alat Tulis'])
            ->assertRedirect(route('kategori.index'));

        $this->assertEquals('Alat Tulis', $kategori->fresh()->kategori);
    }

    /** @test */
    public function nama_kategori_wajib_diisi()
    {
        $this->actingAs($this->user)
            ->from(route('kategori.create'))
            ->post(route('kategori.store'), ['kategori' => ''])
            ->assertRedirect(route('kategori.create'))
            ->assertSessionHasErrors('kategori');
    }

    /** @test */
    public function kategori_default_id_1_tidak_dapat_dihapus()
    {
        $this->assertNotNull(Kategori::find(1));

        $this->actingAs($this->user)
            ->from(route('kategori.index'))
            ->delete(route('kategori.destroy', 1))
            ->assertRedirect(route('kategori.index'));

        $this->assertNotNull(Kategori::find(1));
    }

    /** @test */
    public function hapus_kategori_memindahkan_transaksi_ke_kategori_default_dalam_satu_transaksi()
    {
        $kategori = Kategori::create(['kategori' => 'Sementara']);
        $coa = Coa::create($this->coaData());

        Transaksi::create([
            'tanggal' => '2026-01-01',
            'jenis' => 'pemasukan',
            'kategori_id' => $kategori->id,
            'coa_id' => $coa->id,
            'nominal' => 1000,
            'keterangan' => 'Test',
        ]);

        $this->actingAs($this->user)
            ->delete(route('kategori.destroy', $kategori))
            ->assertRedirect(route('kategori.index'));

        $this->assertNull(Kategori::find($kategori->id));
        $this->assertDatabaseHas('transaksi', ['kategori_id' => 1]);
    }

    /** @test */
    public function hapus_kategori_gagal_jika_kategori_default_tidak_ada()
    {
        $kategori = Kategori::create(['kategori' => 'Sementara']);
        $coa = Coa::create($this->coaData());

        Transaksi::create([
            'tanggal' => '2026-01-01',
            'jenis' => 'pemasukan',
            'kategori_id' => $kategori->id,
            'coa_id' => $coa->id,
            'nominal' => 1000,
            'keterangan' => 'Test',
        ]);

        // Hapus kategori default langsung dari DB untuk mensimulasikan kondisi tidak tersedia
        DB::table('kategori')->where('id', 1)->delete();

        $this->actingAs($this->user)
            ->from(route('kategori.index'))
            ->delete(route('kategori.destroy', $kategori))
            ->assertRedirect(route('kategori.index'));

        $this->assertNotNull(Kategori::find($kategori->id));
    }

    /** @test */
    public function crud_coa_valid_berhasil()
    {
        // Create
        $this->actingAs($this->user)
            ->post(route('coa.store'), $this->coaData())
            ->assertRedirect(route('coa.index'));

        $coa = Coa::where('kode_coa', '101')->first();
        $this->assertNotNull($coa);
        $this->assertEquals(0, (float) $coa->saldo);

        // Edit hanya kode, nama, jenis, cluster
        $this->actingAs($this->user)
            ->put(route('coa.update', $coa), [
                'kode_coa' => '101',
                'nama_coa' => 'Kas Kecil',
                'jenis' => 'Aset',
                'cluster' => null,
            ])
            ->assertRedirect(route('coa.index'));

        $this->assertEquals('Kas Kecil', $coa->fresh()->nama_coa);
    }

    /** @test */
    public function kode_coa_duplikat_ditolak()
    {
        Coa::create($this->coaData());

        $this->actingAs($this->user)
            ->from(route('coa.create'))
            ->post(route('coa.store'), $this->coaData(['nama_coa' => 'Kas Lain']))
            ->assertRedirect(route('coa.create'))
            ->assertSessionHasErrors('kode_coa');
    }

    /** @test */
    public function search_kode_nama_dan_filter_cluster_bekerja()
    {
        $cluster = Cluster::create(['nama' => 'Umum']);
        Coa::create($this->coaData(['cluster' => $cluster->id_cluster]));
        Coa::create([
            'kode_coa' => '201',
            'nama_coa' => 'Utang Usaha',
            'jenis' => 'Liabilitas',
            'cluster' => null,
            'saldo' => 0,
        ]);

        // Search nama
        $this->actingAs($this->user)
            ->get(route('coa.index', ['search' => 'Utang']))
            ->assertOk()
            ->assertSee('Utang Usaha')
            ->assertDontSee('Kas');

        // Filter cluster
        $this->actingAs($this->user)
            ->get(route('coa.index', ['cluster' => $cluster->id_cluster]))
            ->assertOk()
            ->assertSee('Kas')
            ->assertDontSee('Utang Usaha');
    }

    /** @test */
    public function coa_yang_dipakai_transaksi_tidak_dapat_dihapus()
    {
        $coa = Coa::create($this->coaData());
        $kategori = Kategori::create(['kategori' => 'Ops']);

        Transaksi::create([
            'tanggal' => '2026-01-01',
            'jenis' => 'pemasukan',
            'kategori_id' => $kategori->id,
            'coa_id' => $coa->id,
            'nominal' => 1000,
            'keterangan' => 'Test',
        ]);

        $this->actingAs($this->user)
            ->from(route('coa.index'))
            ->delete(route('coa.destroy', $coa))
            ->assertRedirect(route('coa.index'));

        $this->assertNotNull(Coa::find($coa->id));
    }

    /** @test */
    public function coa_bersaldo_non_nol_tidak_dapat_dihapus()
    {
        $coa = Coa::create(array_merge($this->coaData(), ['saldo' => '5000']));

        $this->actingAs($this->user)
            ->from(route('coa.index'))
            ->delete(route('coa.destroy', $coa))
            ->assertRedirect(route('coa.index'));

        $this->assertNotNull(Coa::find($coa->id));
    }

    /** @test */
    public function coa_aman_tanpa_transaksi_dan_bersaldo_nol_dapat_dihapus()
    {
        $coa = Coa::create($this->coaData());

        $this->actingAs($this->user)
            ->delete(route('coa.destroy', $coa))
            ->assertRedirect(route('coa.index'));

        $this->assertNull(Coa::find($coa->id));
    }
}