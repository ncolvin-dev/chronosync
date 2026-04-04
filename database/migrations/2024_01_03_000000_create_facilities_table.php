<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->ulid('facility_id')->primary();
            $table->string('facility_name');
            $table->string('address');
            $table->string('city');
            $table->string('state', 2);
            $table->string('zip', 10);
            $table->string('main_phone', 20);
            $table->string('contact_email');
            $table->integer('clean_time_requirement')->default(0);
            $table->json('credentialing_types')->default('[]');
            $table->boolean('gender_restriction')->default(false);
            $table->boolean('probation_allowed')->default(true);
            $table->string('status')->default('active');
            $table->string('contact1_name')->nullable();
            $table->string('contact1_phone', 20)->nullable();
            $table->string('contact1_email')->nullable();
            $table->string('contact2_name')->nullable();
            $table->string('contact2_phone', 20)->nullable();
            $table->string('contact2_email')->nullable();
            $table->string('timezone')->default('America/New_York');
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('city');
            $table->index('state');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
