<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClusterTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('cluster')) {
            Schema::create('cluster', function (Blueprint $table) {
                $table->integer('id_cluster', true);
                $table->string('nama', 36);
            });
        } elseif (DB::getDriverName() === 'mysql') {
            // Kompatibilitas: tabel existing hanya memiliki KEY (bukan PK/unique).
            // Agar FK coa.cluster valid, pastikan id_cluster menjadi primary key.
            $pk = DB::selectOne("SELECT COUNT(*) AS c FROM information_schema.TABLE_CONSTRAINTS WHERE constraint_schema = DATABASE() AND table_name = 'cluster' AND constraint_type = 'PRIMARY KEY'");
            if ((int) $pk->c === 0) {
                DB::statement('ALTER TABLE cluster ADD PRIMARY KEY (id_cluster)');
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('cluster');
    }
}
