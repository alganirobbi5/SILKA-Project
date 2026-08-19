<?php

namespace Tests\Feature;

use App\Models\Coa;
use App\Models\Kategori;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $kategori;
    protected $kategoriLain;
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
        $this->kategoriLain = Kategori::create(['kategori' => 'Lainnya']);
        $this->coa = Coa::create([
            'kode_coa' => '101',
            'nama_coa' => 'Kas',
            'jenis' => 'Aset',
            'saldo' => 0,
        ]);
    }

    protected function createTransaksi($tanggal, $jenis, $nominal, $kategori = null)
    {
        return Transaksi::create([
            'tanggal' => $tanggal,
            'jenis' => $jenis,
            'kategori_id' => ($kategori ?: $this->kategori)->id,
            'coa_id' => $this->coa->id,
            'nominal' => $nominal,
            'keterangan' => 'Keterangan ' . $nominal,
        ]);
    }

    protected function filterQuery()
    {
        return [
            'kategori_id' => $this->kategori->id,
            'tanggal_awal' => '2026-01-01',
            'tanggal_akhir' => '2026-01-31',
        ];
    }

    /** @test */
    public function form_laporan_dapat_dibuka_tanpa_filter()
    {
        $this->actingAs($this->user)
            ->get(route('laporan.index'))
            ->assertOk()
            ->assertViewHas('hasFilter', false);
    }

    /** @test */
    public function rentang_tanggal_invalid_ditolak()
    {
        $this->actingAs($this->user)
            ->from(route('laporan.index'))
            ->get(route('laporan.index', [
                'tanggal_awal' => '2026-02-10',
                'tanggal_akhir' => '2026-01-01',
            ]))
            ->assertRedirect(route('laporan.index'))
            ->assertSessionHasErrors('tanggal_akhir');
    }

    /** @test */
    public function filter_kategori_dan_tanggal_tepat()
    {
        $this->createTransaksi('2026-01-05', 'pemasukan', 100000);
        $this->createTransaksi('2026-01-20', 'pengeluaran', 40000);
        $this->createTransaksi('2026-02-01', 'pemasukan', 5000); // di luar rentang
        $this->createTransaksi('2026-01-10', 'pemasukan', 9999, $this->kategoriLain); // kategori lain

        $response = $this->actingAs($this->user)
            ->get(route('laporan.index', $this->filterQuery()))
            ->assertOk()
            ->assertViewHas('transaksis', function ($paginator) {
                return $paginator->total() === 2;
            });
    }

    /** @test */
    public function total_pemasukan_pengeluaran_dan_bersih_tepat()
    {
        $this->createTransaksi('2026-01-05', 'pemasukan', 100000);
        $this->createTransaksi('2026-01-10', 'pemasukan', 50000);
        $this->createTransaksi('2026-01-20', 'pengeluaran', 40000);

        $response = $this->actingAs($this->user)
            ->get(route('laporan.index', $this->filterQuery()))
            ->assertOk();

        $response->assertViewHas('totals', function ($totals) {
            return (float) $totals['pemasukan'] === 150000.0
                && (float) $totals['pengeluaran'] === 40000.0
                && (float) $totals['selisih'] === 110000.0;
        });
    }

    /** @test */
    public function web_print_dan_export_memakai_record_yang_sama()
    {
        $this->createTransaksi('2026-01-05', 'pemasukan', 100000);
        $this->createTransaksi('2026-01-20', 'pengeluaran', 40000);
        $this->createTransaksi('2026-02-01', 'pemasukan', 5000);

        $q = $this->filterQuery();

        $web = $this->actingAs($this->user)->get(route('laporan.index', $q));
        $web->assertViewHas('transaksis', function ($p) {
            return $p->total() === 2;
        });

        $print = $this->actingAs($this->user)->get(route('laporan.print', $q));
        $print->assertOk();
        $print->assertViewHas('transaksis', function ($c) {
            return $c->count() === 2;
        });

        $export = $this->actingAs($this->user)->get(route('laporan.export', $q));
        $export->assertOk();
        $export->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /** @test */
    public function export_menghasilkan_file_xlsx_yang_valid()
    {
        $this->createTransaksi('2026-01-05', 'pemasukan', 100000);

        $response = $this->actingAs($this->user)
            ->get(route('laporan.export', $this->filterQuery()))
            ->assertOk();

        $content = $response->streamedContent();
        // ZIP magic bytes: "PK\x03\x04"
        $this->assertSame('PK', substr($content, 0, 2));
        $this->assertStringContainsString('[Content_Types].xml', (string) $content);
    }

    /** @test */
    public function keterangan_yang_menyerupai_formula_tidak_dieksekusi_sebagai_formula_excel()
    {
        Transaksi::create([
            'tanggal' => '2026-01-05',
            'jenis' => 'pemasukan',
            'kategori_id' => $this->kategori->id,
            'coa_id' => $this->coa->id,
            'nominal' => 1000,
            'keterangan' => '=SUM(A1:A2)',
        ]);

        $content = (string) $this->actingAs($this->user)
            ->get(route('laporan.export', $this->filterQuery()))
            ->streamedContent();

        // Unzip untuk memeriksa isi sheet
        $tmp = tempnam(sys_get_temp_dir(), 'silka_');
        file_put_contents($tmp, $content);
        $zip = new \ZipArchive();
        $zip->open($tmp);
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        @unlink($tmp);

        // Nilai diawali apostrof agar tidak dieksekusi sebagai formula
        $this->assertStringContainsString("&apos;=SUM(A1:A2)", (string) $sheetXml);
    }
}