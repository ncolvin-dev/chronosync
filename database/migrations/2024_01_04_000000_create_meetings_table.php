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
        Schema::create('meetings', function (Blueprint $table) {
            $table->ulid('meeting_id')->primary();
            $table->foreignUlid('facility_id')->constrained('facilities', 'facility_id')->onDelete('cascade');
            $table->date('meeting_date');
            $table->time('meeting_time');
            $table->enum('format', ['in_person', 'virtual'])->default('in_person');
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->foreignUlid('assigned_volunteer_id')->nullable()->constrained('volunteers', 'volunteer_id')->onDelete('set null');
            $table->enum('confirmed_status', ['pending', 'confirmed', 'declined'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->tinyInteger('week_of_month'); // 1-5
            $table->tinyInteger('day_of_week'); // 1-7 (Monday = 1, Sunday = 7)
            $table->timestamps();

            // Indexes
            $table->index('facility_id');
            $table->index('assigned_volunteer_id');
            $table->index('meeting_date');
            $table->index('status');
            $table->index(['facility_id', 'meeting_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
