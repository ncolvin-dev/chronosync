<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sms_config', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->integer('hours_before_meeting')->default(24);
            $table->tinyInteger('daytime_start')->default(8); // Hour (0-23)
            $table->tinyInteger('daytime_end')->default(20); // Hour (0-23)
            $table->integer('buffer_minutes')->default(60);
            $table->string('timezone')->default('America/Chicago');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_config');
    }
};
