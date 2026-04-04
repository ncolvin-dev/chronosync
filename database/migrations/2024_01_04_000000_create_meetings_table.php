<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->ulid('meeting_id')->primary();
            $table->foreignUlid('facility_id')->constrained('facilities', 'facility_id')->onDelete('cascade');
            $table->dateTime('scheduled_time');
            $table->integer('duration_minutes')->default(60);
            $table->string('status')->default('scheduled');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index('facility_id');
            $table->index('scheduled_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
