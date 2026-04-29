<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            $table->date('dob')->nullable()->change();
            $table->date('clean_date')->nullable()->change();
            $table->string('gender')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            $table->date('dob')->nullable(false)->change();
            $table->date('clean_date')->nullable(false)->change();
            $table->string('gender')->nullable(false)->change();
        });
    }
};
