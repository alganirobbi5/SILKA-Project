<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddFinancialForeignKeys extends Migration
{
    public function up()
    {
        $driver = DB::getDriverName();
        if ($driver !== 'mysql') {
            return;
        }

        if (Schema::hasTable('transaksi')) {
            // transaksi.kategori_id -> kategori.id (RESTRICT)
            $fk = DB::selectOne("SELECT COUNT(*) AS c FROM information_schema.TABLE_CONSTRAINTS WHERE constraint_schema = DATABASE() AND table_name = 'transaksi' AND constraint_name = 'transaksi_kategori_id_foreign'");
            if ((int) $fk->c === 0) {
                DB::statement('ALTER TABLE transaksi ADD CONSTRAINT transaksi_kategori_id_foreign FOREIGN KEY (kategori_id) REFERENCES kategori (id) ON DELETE RESTRICT');
            }

            // transaksi.coa_id -> coa.id (RESTRICT)
            $fk = DB::selectOne("SELECT COUNT(*) AS c FROM information_schema.TABLE_CONSTRAINTS WHERE constraint_schema = DATABASE() AND table_name = 'transaksi' AND constraint_name = 'transaksi_coa_id_foreign'");
            if ((int) $fk->c === 0) {
                DB::statement('ALTER TABLE transaksi ADD CONSTRAINT transaksi_coa_id_foreign FOREIGN KEY (coa_id) REFERENCES coa (id) ON DELETE RESTRICT');
            }
        }

        if (Schema::hasTable('coa')) {
            // coa.cluster -> cluster.id_cluster
            $fk = DB::selectOne("SELECT COUNT(*) AS c FROM information_schema.TABLE_CONSTRAINTS WHERE constraint_schema = DATABASE() AND table_name = 'coa' AND constraint_name = 'coa_cluster_foreign'");
            if ((int) $fk->c === 0) {
                DB::statement('ALTER TABLE coa ADD CONSTRAINT coa_cluster_foreign FOREIGN KEY (cluster) REFERENCES cluster (id_cluster) ON DELETE RESTRICT');
            }
        }
    }

    public function down()
    {
        $driver = DB::getDriverName();
        if ($driver !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE transaksi DROP FOREIGN KEY IF EXISTS transaksi_kategori_id_foreign');
        DB::statement('ALTER TABLE transaksi DROP FOREIGN KEY IF EXISTS transaksi_coa_id_foreign');
        DB::statement('ALTER TABLE coa DROP FOREIGN KEY IF EXISTS coa_cluster_foreign');
    }
}
