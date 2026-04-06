<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_assignments', function (Blueprint $table) {
            $table->ulid('meeting_assignment_id')->primary();

            // Which recurring meeting slot this assignment covers
            $table->foreignUlid('meeting_id')
                ->constrained('meetings', 'meeting_id')
                ->onDelete('cascade');

            // Which volunteer is assigned
            $table->foreignUlid('volunteer_id')
                ->constrained('volunteers', 'volunteer_id')
                ->onDelete('cascade');

            // The specific calendar date of the occurrence being covered
            // e.g., if the meeting is "every 1st Tuesday", this might be 2025-10-07
            $table->date('assignment_date');

            // Workflow status
            $table->string('status')->default('pending_confirmation');
            // Allowed: pending_confirmation, confirmed, declined, cancelled

            // How the assignment was created
            $table->string('assignment_type')->default('auto');
            // Allowed: auto, manual

            // Reason required for manual coordinator overrides
            $table->text('override_reason')->nullable();

            // When the volunteer confirmed (or null if not yet)
            $table->dateTime('confirmed_at')->nullable();

            $table->timestamps();

            // A volunteer can only be assigned once per meeting occurrence
            $table->unique(['meeting_id', 'volunteer_id', 'assignment_date'], 'unique_volunteer_per_occurrence');

            $table->index('meeting_id');
            $table->index('volunteer_id');
            $table->index('assignment_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_assignments');
    }
};
