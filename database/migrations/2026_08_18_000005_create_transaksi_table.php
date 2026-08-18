<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('transaksi')) {
            Schema::create('transaksi', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->date('tanggal');
                $table->enum('jenis', ['pemasukan', 'pengeluaran']);
                $table->bigInteger('kategori_id')->unsigned();
                $table->bigInteger('coa_id')->unsigned()->nullable();
                $table->decimal('nominal', 18, 2);
                $table->text('keterangan')->nullable();
                $table->timestamps();

                $table->index('tanggal', 'transaksi_tanggal_index');
                $table->index('kategori_id', 'transaksi_kategori_id_index');
                $table->index('coa_id', 'transaksi_coa_id_index');
            });
            return;
        }

        $driver = DB::getDriverName();
        if ($driver !== 'mysql') {
            return;
        }

        // ---- Schema compatibility untuk database existing ----

        if (!Schema::hasColumn('transaksi', 'coa_id')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->bigInteger('coa_id')->unsigned()->nullable()->after('kategori_id');
            });
        }

        // index tanggal
        $hasTanggalIdx = (bool) DB::selectOne("SHOW INDEX FROM transaksi WHERE Key_name = 'transaksi_tanggal_index'");
        if (!$hasTanggalIdx) {
            DB::statement('ALTER TABLE transaksi ADD INDEX transaksi_tanggal_index (tanggal)');
        }

        $hasKategoriIdx = (bool) DB::selectOne("SHOW INDEX FROM transaksi WHERE Key_name = 'transaksi_kategori_id_index'");
        if (!$hasKategoriIdx) {
            DB::statement('ALTER TABLE transaksi ADD INDEX transaksi_kategori_id_index (kategori_id)');
        }

        $hasCoaIdx = (bool) DB::selectOne("SHOW INDEX FROM transaksi WHERE Key_name = 'transaksi_coa_id_index'");
        if (!$hasCoaIdx) {
            DB::statement('ALTER TABLE transaksi ADD INDEX transaksi_coa_id_index (coa_id)');
        }

        // kategori_id: samakan tipe dengan kategori.id (bigint unsigned)
        $kategoriCol = DB::selectOne("SHOW COLUMNS FROM transaksi WHERE Field = 'kategori_id'");
        if ($kategoriCol && stripos($kategoriCol->Type, 'bigint') === false) {
            DB::statement('ALTER TABLE transaksi MODIFY kategori_id BIGINT UNSIGNED NOT NULL');
        }

        // nominal: bigint -> DECIMAL(18,2)
        $nominalCol = DB::selectOne("SHOW COLUMNS FROM transaksi WHERE Field = 'nominal'");
        if ($nominalCol && stripos($nominalCol->Type, 'decimal') === false) {
            DB::statement('ALTER TABLE transaksi MODIFY nominal DECIMAL(18,2) NOT NULL');
        }

        // jenis: normalisasi nilai ke canonical lowercase + ubah enum
        $jenisCol = DB::selectOne("SHOW COLUMNS FROM transaksi WHERE Field = 'jenis'");
        if ($jenisCol && strpos($jenisCol->Type, 'pemasukan') === false) {
            // 1) longgarkan dulu ke varchar agar nilai legacy tetap tersimpan
            DB::statement("ALTER TABLE transaksi MODIFY jenis VARCHAR(20) NOT NULL");
            // 2) backfill eksplisit nilai legacy
            DB::table('transaksi')->where('jenis', 'Pemasukan')->update(['jenis' => 'pemasukan']);
            DB::table('transaksi')->where('jenis', 'Pengeluaran')->update(['jenis' => 'pengeluaran']);
            // 3) kembalikan ke enum canonical
            DB::statement("ALTER TABLE transaksi MODIFY jenis ENUM('pemasukan','pengeluaran') NOT NULL");
        }
    }

    public function down()
    {
        // Kompatibilitas tidak dibalik otomatis; down migration sengaja kosong
        // agar tidak merusak data legacy.
    }
}
