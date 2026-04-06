<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            // Make scheduled_time nullable — it's no longer the primary way to define a meeting
            $table->dateTime('scheduled_time')->nullable()->change();

            // Recurring pattern fields
            $table->tinyInteger('day_of_week')->nullable()->after('facility_id')
                ->comment('0=Sunday, 1=Monday, ..., 6=Saturday');
            $table->tinyInteger('week_of_month')->nullable()->after('day_of_week')
                ->comment('1-4 = specific week, 5 = last week of month');
            $table->time('meeting_time')->nullable()->after('week_of_month');
            $table->string('format')->default('in_person')->after('meeting_time')
                ->comment('in_person, virtual, hybrid');

            // How many volunteers are needed per occurrence (max 5)
            $table->tinyInteger('volunteers_needed')->default(1)->after('format');

            // Index for common queries
            $table->index(['facility_id', 'day_of_week', 'week_of_month']);
        });
    }

    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropIndex(['facility_id', 'day_of_week', 'week_of_month']);
            $table->dropColumn(['day_of_week', 'week_of_month', 'meeting_time', 'format', 'volunteers_needed']);
            $table->dateTime('scheduled_time')->nullable(false)->change();
        });
    }
};
