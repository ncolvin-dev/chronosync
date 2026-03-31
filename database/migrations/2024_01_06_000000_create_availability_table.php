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
        Schema::create('availability', function (Blueprint $table) {
            $table->ulid('availability_id')->primary();
            $table->foreignUlid('volunteer_id')->constrained('volunteers', 'volunteer_id')->onDelete('cascade');
            $table->tinyInteger('week_of_month'); // 1-5
            $table->tinyInteger('day_of_week'); // 1-7 (Monday = 1, Sunday = 7)
            $table->tinyInteger('hour_start'); // 8-21
            $table->tinyInteger('hour_end'); // 9-22
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('volunteer_id');
            $table->index(['volunteer_id', 'week_of_month', 'day_of_week']);
            $table->unique(['volunteer_id', 'week_of_month', 'day_of_week', 'hour_start', 'hour_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availability');
    }
};
