<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedDefaultKategoriAndReassignOrphans extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('kategori')) {
            return;
        }

        // Kategori default ID 1 (CAT-002) - idempotent
        $exists = DB::table('kategori')->where('id', 1)->exists();
        if (!$exists) {
            DB::table('kategori')->insert([
                'id' => 1,
                'kategori' => 'Tanpa Kategori',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Pembersihan data eksplisit (PRD 8.4.9):
        // Seluruh transaksi yang menunjuk kategori yang sudah tidak ada
        // dipindahkan ke kategori default ID 1 dalam satu operasi atomik.
        $orphans = DB::select(
            'SELECT t.id FROM transaksi t LEFT JOIN kategori k ON t.kategori_id = k.id WHERE k.id IS NULL'
        );
        if (count($orphans) > 0) {
            $ids = array_map(function ($row) {
                return $row->id;
            }, $orphans);
            DB::transaction(function () use ($ids) {
                foreach (array_chunk($ids, 500) as $chunk) {
                    DB::table('transaksi')
                        ->whereIn('id', $chunk)
                        ->update(['kategori_id' => 1]);
                }
            });
        }
    }

    public function down()
    {
        // Data cleanup tidak dibalik otomatis.
    }
}
