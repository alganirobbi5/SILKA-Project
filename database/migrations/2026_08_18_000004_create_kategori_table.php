<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKategoriTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('kategori')) {
            Schema::create('kategori', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('kategori', 255);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('kategori');
    }
}
