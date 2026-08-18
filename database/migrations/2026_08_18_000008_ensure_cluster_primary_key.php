<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnsureClusterPrimaryKey extends Migration
{
    public function up()
    {
        if (DB::getDriverName() !== 'mysql' || !Schema::hasTable('cluster')) {
            return;
        }

        // Agar FK coa.cluster valid, id_cluster harus primary/unique.
        $pk = DB::selectOne("SELECT COUNT(*) AS c FROM information_schema.TABLE_CONSTRAINTS WHERE constraint_schema = DATABASE() AND table_name = 'cluster' AND constraint_type = 'PRIMARY KEY'");
        if ((int) $pk->c === 0) {
            DB::statement('ALTER TABLE cluster ADD PRIMARY KEY (id_cluster)');
        }
    }

    public function down()
    {
        // Tidak dibalik otomatis.
    }
}