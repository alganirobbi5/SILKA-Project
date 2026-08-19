<?php

namespace Tests\Feature;

use App\Models\Coa;
use App\Models\Kategori;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TransaksiSaldoTest extends TestCase
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

        $this->kategori = Kategori::create(['kategori' => 'Operasional']);
        $this->coa = Coa::create([
            'kode_coa' => '101',
            'nama_coa' => 'Kas',
            'jenis' => 'Aset',
            'saldo' => 0,
        ]);
    }

    protected function payload(array $overrides = [])
    {
        return array_merge([
            'tanggal' => '2026-01-10',
            'jenis' => 'pemasukan',
            'kategori_id' => $this->kategori->id,
            'coa_id' => $this->coa->id,
            'nominal' => '100000.50',
            'keterangan' => 'Pemasukan kas',
        ], $overrides);
    }

    /** @test */
    public function create_pemasukan_menambah_saldo_coa()
    {
        $this->actingAs($this->user)
            ->post(route('transaksi.store'), $this->payload())
            ->assertRedirect(route('transaksi.index'));

        $this->assertDatabaseHas('transaksi', [
            'coa_id' => $this->coa->id,
            'nominal' => 100000.50,
            'jenis' => 'pemasukan',
        ]);
        $this->assertEquals(100000.50, (float) $this->coa->fresh()->saldo);
    }

    /** @test */
    public function create_pengeluaran_mengurangi_saldo_coa()
    {
        $this->actingAs($this->user)
            ->post(route('transaksi.store'), $this->payload([
                'jenis' => 'pengeluaran',
                'nominal' => '50000',
            ]))
            ->assertRedirect(route('transaksi.index'));

        $this->assertEquals(-50000.00, (float) $this->coa->fresh()->saldo);
    }

    /** @test */
    public function nominal_nol_atau_negatif_ditolak_tanpa_perubahan_saldo()
    {
        foreach (['0', '-1000'] as $nominal) {
            $this->actingAs($this->user)
                ->from(route('transaksi.create'))
                ->post(route('transaksi.store'), $this->payload(['nominal' => $nominal]))
                ->assertRedirect(route('transaksi.create'))
                ->assertSessionHasErrors('nominal');
        }

        $this->assertDatabaseCount('transaksi', 0);
        $this->assertEquals(0, (float) $this->coa->fresh()->saldo);
    }

    /** @test */
    public function kategori_id_tidak_valid_ditolak_tanpa_perubahan_saldo()
    {
        $this->actingAs($this->user)
            ->from(route('transaksi.create'))
            ->post(route('transaksi.store'), $this->payload(['kategori_id' => 99999]))
            ->assertSessionHasErrors('kategori_id');

        $this->assertDatabaseCount('transaksi', 0);
        $this->assertEquals(0, (float) $this->coa->fresh()->saldo);
    }

    /** @test */
    public function coa_id_tidak_valid_ditolak_tanpa_transaksi_tersimpan()
    {
        $this->actingAs($this->user)
            ->from(route('transaksi.create'))
            ->post(route('transaksi.store'), $this->payload(['coa_id' => 99999]))
            ->assertSessionHasErrors('coa_id');

        $this->assertDatabaseCount('transaksi', 0);
    }

    protected function createTransaksi(array $overrides = [])
    {
        return app(\App\Services\TransaksiSaldoService::class)->create($this->payload($overrides));
    }

    /** @test */
    public function edit_nominal_pada_coa_yang_sama_menghasilkan_saldo_tepat()
    {
        $transaksi = $this->createTransaksi(['nominal' => '100000.50']);

        $this->actingAs($this->user)
            ->put(route('transaksi.update', $transaksi), $this->payload(['nominal' => '200000']))
            ->assertRedirect(route('transaksi.index'));

        $this->assertEquals(200000.00, (float) $this->coa->fresh()->saldo);
    }

    /** @test */
    public function edit_jenis_pemasukan_menjadi_pengeluaran_menghasilkan_saldo_tepat()
    {
        $transaksi = $this->createTransaksi(['nominal' => '100000']);
        $this->assertEquals(100000.00, (float) $this->coa->fresh()->saldo);

        $this->actingAs($this->user)
            ->put(route('transaksi.update', $transaksi), $this->payload([
                'jenis' => 'pengeluaran',
                'nominal' => '40000',
            ]))
            ->assertRedirect(route('transaksi.index'));

        $this->assertEquals(-40000.00, (float) $this->coa->fresh()->saldo);
    }

    /** @test */
    public function edit_coa_merevert_akun_lama_dan_menerapkan_akun_baru()
    {
        $coaBaru = Coa::create([
            'kode_coa' => '102',
            'nama_coa' => 'Bank',
            'jenis' => 'Aset',
            'saldo' => 0,
        ]);

        $transaksi = $this->createTransaksi(['nominal' => '100000']);

        $this->actingAs($this->user)
            ->put(route('transaksi.update', $transaksi), $this->payload([
                'coa_id' => $coaBaru->id,
                'nominal' => '75000',
            ]))
            ->assertRedirect(route('transaksi.index'));

        $this->assertEquals(0, (float) $this->coa->fresh()->saldo);
        $this->assertEquals(75000.00, (float) $coaBaru->fresh()->saldo);
        $this->assertDatabaseHas('transaksi', [
            'id' => $transaksi->id,
            'coa_id' => $coaBaru->id,
        ]);
    }

    /** @test */
    public function delete_pemasukan_merevert_saldo()
    {
        $transaksi = $this->createTransaksi(['nominal' => '100000']);
        $this->assertEquals(100000.00, (float) $this->coa->fresh()->saldo);

        $this->actingAs($this->user)
            ->delete(route('transaksi.destroy', $transaksi))
            ->assertRedirect(route('transaksi.index'));

        $this->assertDatabaseMissing('transaksi', ['id' => $transaksi->id]);
        $this->assertEquals(0, (float) $this->coa->fresh()->saldo);
    }

    /** @test */
    public function delete_pengeluaran_merevert_saldo()
    {
        $transaksi = $this->createTransaksi([
            'jenis' => 'pengeluaran',
            'nominal' => '50000',
        ]);
        $this->assertEquals(-50000.00, (float) $this->coa->fresh()->saldo);

        $this->actingAs($this->user)
            ->delete(route('transaksi.destroy', $transaksi))
            ->assertRedirect(route('transaksi.index'));

        $this->assertDatabaseMissing('transaksi', ['id' => $transaksi->id]);
        $this->assertEquals(0, (float) $this->coa->fresh()->saldo);
    }

    /** @test */
    public function exception_saat_update_coa_me_rollback_transaksi()
    {
        $transaksi = $this->createTransaksi(['nominal' => '100000']);

        // Simulasi: coa tujuan dihapus di tengah proses (melalui service yang di-mock)
        $this->mock(\App\Services\TransaksiSaldoService::class, function ($mock) {
            $mock->shouldReceive('update')->once()->andThrow(new \Exception('Gagal'));
        });

        $this->actingAs($this->user)
            ->from(route('transaksi.edit', $transaksi))
            ->put(route('transaksi.update', $transaksi), $this->payload(['nominal' => '200000']))
            ->assertRedirect(route('transaksi.edit', $transaksi));

        // Saldo dan data transaksi tidak berubah
        $this->assertEquals(100000.00, (float) $this->coa->fresh()->saldo);
        $this->assertDatabaseHas('transaksi', ['id' => $transaksi->id, 'nominal' => 100000.00]);
    }

    /** @test */
    public function pagination_transaksi_maksimal_20_record()
    {
        for ($i = 1; $i <= 25; $i++) {
            Transaksi::create($this->payload([
                'tanggal' => '2026-01-' . str_pad(min($i, 28), 2, '0', STR_PAD_LEFT),
                'nominal' => (string) $i,
                'keterangan' => 'Transaksi ke-' . $i,
            ]));
        }

        $response = $this->actingAs($this->user)
            ->get(route('transaksi.index'))
            ->assertOk();

        $response->assertViewHas('transaksis', function ($paginator) {
            return $paginator->count() === 20 && $paginator->total() === 25;
        });
    }

    /** @test */
    public function filter_tanggal_dan_keterangan_menghasilkan_data_yang_benar()
    {
        Transaksi::create($this->payload(['tanggal' => '2026-01-05', 'keterangan' => 'Gaji karyawan']));
        Transaksi::create($this->payload(['tanggal' => '2026-02-10', 'keterangan' => 'Tagihan listrik']));
        Transaksi::create($this->payload(['tanggal' => '2026-03-15', 'keterangan' => 'Gaji karyawan bonus']));

        $this->actingAs($this->user)
            ->get(route('transaksi.index', [
                'tanggal_awal' => '2026-01-01',
                'tanggal_akhir' => '2026-01-31',
                'search' => 'Gaji',
            ]))
            ->assertOk()
            ->assertSee('Gaji karyawan')
            ->assertDontSee('Tagihan listrik');
    }

    /** @test */
    public function urutan_default_tanggal_terbaru_lalu_id_terbaru()
    {
        Transaksi::create($this->payload(['tanggal' => '2026-01-10', 'keterangan' => 'A']));
        Transaksi::create($this->payload(['tanggal' => '2026-01-10', 'keterangan' => 'B']));

        $paginator = $this->actingAs($this->user)
            ->get(route('transaksi.index'))
            ->viewData('transaksis');

        $this->assertEquals('B', $paginator->first()->keterangan);
        $this->assertEquals('A', $paginator->last()->keterangan);
    }
}