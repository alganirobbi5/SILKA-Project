<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTargetCapaiansTable extends Migration
{
    public function up()
    {
        // Tabel dibuat di luar Laravel pada database existing;
        // migration ini memastikan instalasi baru tetap mendapatkannya (PRD 8.1).
        if (Schema::hasTable('target_capaians')) {
            return;
        }

        Schema::create('target_capaians', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('tahun');
            $table->decimal('target_capaian', 18, 2);
            $table->timestamps();

            $table->unique('tahun', 'target_capaians_tahun_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('target_capaians');
    }
}