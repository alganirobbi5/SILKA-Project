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

class DashboardTargetTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $kategori;
    protected $coa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Bendahara',
            'email' => 'bendahara@example.test',
            'password' => Hash::make('password123'),
            'level' => 'bendahara',
        ]);

        $this->kategori = Kategori::create(['kategori' => 'Ops']);
        $this->coa = Coa::create([
            'kode_coa' => '101',
            'nama_coa' => 'Kas',
            'jenis' => 'Aset',
            'saldo' => 0,
        ]);
    }

    protected function createTransaksi($tanggal, $jenis, $nominal)
    {
        return Transaksi::create([
            'tanggal' => $tanggal,
            'jenis' => $jenis,
            'kategori_id' => $this->kategori->id,
            'coa_id' => $this->coa->id,
            'nominal' => $nominal,
            'keterangan' => 'Test',
        ]);
    }

    /** @test */
    public function tanpa_parameter_tahun_menggunakan_tahun_berjalan()
    {
        $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('year', (int) date('Y'));
    }

    /** @test */
    public function tanpa_parameter_tahun_menggunakan_tahun_data_terbaru_saat_data_lebih_lama()
    {
        $this->createTransaksi('2024-06-15', 'pemasukan', 100000);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('year', 2024);

        // Selector hanya memuat tahun yang benar-benar memiliki data transaksi,
        // tanpa tahun sistem berjalan yang tidak memiliki data.
        $response->assertViewHas('tahunTersedia', function ($tahun) {
            $all = $tahun->all();
            return in_array(2024, $all) && !in_array((int) date('Y'), $all);
        });
    }

    /** @test */
    public function parameter_tahun_valid_menggunakan_tahun_tersebut()
    {
        $this->actingAs($this->user)
            ->get(route('dashboard', ['year' => '2024']))
            ->assertOk()
            ->assertViewHas('year', 2024);
    }

    /** @test */
    public function parameter_tahun_invalid_memakai_fallback_dan_tidak_error()
    {
        $this->actingAs($this->user)
            ->get(route('dashboard', ['year' => 'abcd']))
            ->assertOk()
            ->assertViewHas('year', (int) date('Y'));

        $this->actingAs($this->user)
            ->get(route('dashboard', ['year' => '9999']))
            ->assertOk()
            ->assertViewHas('year', (int) date('Y'));
    }

    /** @test */
    public function parameter_tahun_invalid_memakai_fallback_data_terbaru_saat_data_lebih_lama()
    {
        $this->createTransaksi('2024-06-15', 'pemasukan', 100000);

        $this->actingAs($this->user)
            ->get(route('dashboard', ['year' => 'abcd']))
            ->assertOk()
            ->assertViewHas('year', 2024);

        $this->actingAs($this->user)
            ->get(route('dashboard', ['year' => '9999']))
            ->assertOk()
            ->assertViewHas('year', 2024);
    }

    /** @test */
    public function agregat_hari_bulan_dan_tahun_tepat()
    {
        $this->createTransaksi(date('Y-m-d'), 'pemasukan', 100000);
        $this->createTransaksi(date('Y-m-d'), 'pengeluaran', 30000);
        $this->createTransaksi(date('Y-m-01'), 'pemasukan', 50000);
        $this->createTransaksi(date('Y') . '-01-01', 'pemasukan', 20000);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard', ['year' => date('Y')]))
            ->assertOk();

        $response->assertViewHas('hariIni', function ($row) {
            return (float) $row->pemasukan === 100000.0 && (float) $row->pengeluaran === 30000.0;
        });

        $response->assertViewHas('bulanIni', function ($row) {
            return (float) $row->pemasukan === 150000.0 && (float) $row->pengeluaran === 30000.0;
        });

        $response->assertViewHas('tahunIni', function ($row) {
            return (float) $row->pemasukan === 170000.0 && (float) $row->pengeluaran === 30000.0;
        });
    }

    /** @test */
    public function tahun_tanpa_transaksi_menampilkan_nol()
    {
        $this->createTransaksi(date('Y') . '-01-01', 'pemasukan', 100000);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard', ['year' => '2030']))
            ->assertOk();

        $response->assertViewHas('tahunIni', function ($row) {
            return (float) $row->pemasukan === 0.0 && (float) $row->pengeluaran === 0.0;
        });
    }

    /** @test */
    public function target_tahun_terpilih_muncul_di_dashboard()
    {
        TargetCapaian::create(['tahun' => 2024, 'target_capaian' => 5000000]);

        $this->actingAs($this->user)
            ->get(route('dashboard', ['year' => '2024']))
            ->assertOk()
            ->assertViewHas('target', function ($target) {
                return $target && (float) $target->target_capaian === 5000000.0;
            });
    }

    /** @test */
    public function crud_target_capaian_berhasil()
    {
        // Create
        $this->actingAs($this->user)
            ->post(route('target-capaians.store'), [
                'tahun' => 2026,
                'target_capaian' => 10000000,
            ])
            ->assertRedirect(route('target-capaians.index'));

        $target = TargetCapaian::where('tahun', 2026)->first();
        $this->assertNotNull($target);

        // Edit
        $this->actingAs($this->user)
            ->put(route('target-capaians.update', $target), [
                'tahun' => 2026,
                'target_capaian' => 15000000,
            ])
            ->assertRedirect(route('target-capaians.index'));

        $this->assertEquals(15000000.00, (float) $target->fresh()->target_capaian);

        // Delete
        $this->actingAs($this->user)
            ->delete(route('target-capaians.destroy', $target))
            ->assertRedirect(route('target-capaians.index'));

        $this->assertNull(TargetCapaian::find($target->id));
    }

    /** @test */
    public function target_nominal_negatif_ditolak()
    {
        $this->actingAs($this->user)
            ->from(route('target-capaians.create'))
            ->post(route('target-capaians.store'), [
                'tahun' => 2026,
                'target_capaian' => -100,
            ])
            ->assertRedirect(route('target-capaians.create'))
            ->assertSessionHasErrors('target_capaian');
    }

    /** @test */
    public function duplikasi_target_pada_tahun_yang_sama_ditolak()
    {
        TargetCapaian::create(['tahun' => 2026, 'target_capaian' => 10000000]);

        $this->actingAs($this->user)
            ->from(route('target-capaians.create'))
            ->post(route('target-capaians.store'), [
                'tahun' => 2026,
                'target_capaian' => 20000000,
            ])
            ->assertRedirect(route('target-capaians.create'))
            ->assertSessionHasErrors('tahun');
    }
}