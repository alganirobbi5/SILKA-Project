<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NormalizeTargetCapaiansTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('target_capaians')) {
            return;
        }

        $driver = DB::getDriverName();
        if ($driver !== 'mysql') {
            return;
        }

        // target_capaian: bigint -> DECIMAL(18,2)
        $col = DB::selectOne("SHOW COLUMNS FROM target_capaians WHERE Field = 'target_capaian'");
        if ($col && stripos($col->Type, 'decimal') === false) {
            DB::statement('ALTER TABLE target_capaians MODIFY target_capaian DECIMAL(18,2) NOT NULL');
        }

        // unique tahun (data existing unik: 2022, 2023, 2024)
        $row = DB::selectOne(
            "SELECT COUNT(*) AS c FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'target_capaians' AND index_name = 'target_capaians_tahun_unique'"
        );
        if (!$row || (int) $row->c === 0) {
            DB::statement('ALTER TABLE target_capaians ADD UNIQUE INDEX target_capaians_tahun_unique (tahun)');
        }
    }

    public function down()
    {
        // Kompatibilitas tidak dibalik otomatis.
    }
}
