<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('availability', function (Blueprint $table) {
            $table->integer('hour_end')->after('hour_start')->default(0);
        });

        // Each existing slot covers exactly one hour
        DB::statement('UPDATE availability SET hour_end = hour_start + 1');
    }

    public function down(): void
    {
        Schema::table('availability', function (Blueprint $table) {
            $table->dropColumn('hour_end');
        });
    }
};
