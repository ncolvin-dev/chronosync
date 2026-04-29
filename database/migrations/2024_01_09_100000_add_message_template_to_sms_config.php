<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sms_config', function (Blueprint $table) {
            $table->text('message_template')
                ->default('Reminder: H&I meeting at {facility_name} on {meeting_date} at {meeting_time}. Thank you!')
                ->after('buffer_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('sms_config', function (Blueprint $table) {
            $table->dropColumn('message_template');
        });
    }
};
