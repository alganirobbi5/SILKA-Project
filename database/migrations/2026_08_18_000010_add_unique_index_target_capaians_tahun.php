<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddUniqueIndexTargetCapaiansTahun extends Migration
{
    public function up()
    {
        if (DB::getDriverName() !== 'mysql' || !Schema::hasTable('target_capaians')) {
            return;
        }

        $row = DB::selectOne(
            "SELECT COUNT(*) AS c FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'target_capaians' AND index_name = 'target_capaians_tahun_unique'"
        );
        if (!$row || (int) $row->c === 0) {
            DB::statement('ALTER TABLE target_capaians ADD UNIQUE INDEX target_capaians_tahun_unique (tahun)');
        }
    }

    public function down()
    {
        // Tidak dibalik otomatis.
    }
}