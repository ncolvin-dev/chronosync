<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('availability', function (Blueprint $table) {
            $table->ulid('availability_id')->primary();
            $table->foreignUlid('volunteer_id')->constrained('volunteers', 'volunteer_id')->onDelete('cascade');
            $table->integer('week_of_month');
            $table->integer('day_of_week');
            $table->integer('hour_start');
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->unique(['volunteer_id', 'week_of_month', 'day_of_week', 'hour_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availability');
    }
};
