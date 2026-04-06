<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volunteer_credentials', function (Blueprint $table) {
            $table->ulid('credential_id')->primary();
            $table->foreignUlid('volunteer_id')->constrained('volunteers', 'volunteer_id')->onDelete('cascade');
            $table->foreignUlid('facility_id')->constrained('facilities', 'facility_id')->onDelete('cascade');
            $table->foreignUlid('credential_type_id')->constrained('credential_types', 'credential_type_id')->onDelete('cascade');
            $table->string('status')->default('pending');
            $table->date('approval_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['volunteer_id', 'facility_id', 'credential_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteer_credentials');
    }
};
