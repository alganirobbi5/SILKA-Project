<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddLevelAndFotoToUsersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'level')) {
            DB::statement("ALTER TABLE users ADD COLUMN level ENUM('admin','bendahara') NOT NULL DEFAULT 'bendahara' AFTER email_verified_at");
        }

        if (!Schema::hasColumn('users', 'foto')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('foto')->nullable()->after('level');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'foto')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('foto');
            });
        }

        if (Schema::hasColumn('users', 'level')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('level');
            });
        }
    }
}
