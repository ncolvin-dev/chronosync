<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_config', function (Blueprint $table) {
            $table->ulid('config_id')->primary();
            $table->integer('hours_before_meeting')->default(24);
            $table->integer('daytime_start')->default(8);
            $table->integer('daytime_end')->default(20);
            $table->integer('buffer_minutes')->default(60);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_config');
    }
};
