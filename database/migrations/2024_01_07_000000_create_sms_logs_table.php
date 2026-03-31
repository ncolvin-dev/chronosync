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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->ulid('sms_id')->primary();
            $table->foreignUlid('meeting_id')->nullable()->constrained('meetings', 'meeting_id')->onDelete('set null');
            $table->foreignUlid('volunteer_id')->constrained('volunteers', 'volunteer_id')->onDelete('cascade');
            $table->enum('message_type', ['reminder', 'confirmation', 'replacement', 'cancellation']);
            $table->text('content');
            $table->timestamp('sent_time');
            $table->enum('response', ['YES', 'NO'])->nullable();
            $table->timestamp('response_time')->nullable();
            $table->enum('status', ['pending', 'delivered', 'failed'])->default('pending');
            $table->tinyInteger('retry_count')->default(0);
            $table->timestamps();

            // Indexes
            $table->index('meeting_id');
            $table->index('volunteer_id');
            $table->index('sent_time');
            $table->index('status');
            $table->index('message_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
