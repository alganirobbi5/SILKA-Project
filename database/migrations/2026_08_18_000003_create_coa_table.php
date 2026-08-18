<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoaTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('coa')) {
            Schema::create('coa', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('kode_coa', 255);
                $table->string('nama_coa', 255);
                $table->string('jenis', 255);
                $table->integer('cluster')->nullable();
                $table->decimal('saldo', 18, 2)->default(0);
                $table->timestamps();
                $table->unique('kode_coa', 'coa_kode_coa_unique');
            });
        } elseif (!Schema::hasColumn('coa', 'saldo')) {
            Schema::table('coa', function (Blueprint $table) {
                $table->decimal('saldo', 18, 2)->default(0)->after('cluster');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('coa');
    }
}
