<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->ulid('sms_log_id')->primary();
            $table->foreignUlid('volunteer_id')->constrained('volunteers', 'volunteer_id')->onDelete('cascade');
            $table->foreignUlid('meeting_id')->nullable()->constrained('meetings', 'meeting_id')->onDelete('cascade');
            $table->string('phone_number');
            $table->text('message_body');
            $table->string('status')->default('sent');
            $table->string('response')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamps();
            $table->index('volunteer_id');
            $table->index('meeting_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
