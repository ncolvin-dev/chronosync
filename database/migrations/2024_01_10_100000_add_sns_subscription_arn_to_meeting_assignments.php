<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meeting_assignments', function (Blueprint $table) {
            $table->string('sns_subscription_arn')->nullable()->after('confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('meeting_assignments', function (Blueprint $table) {
            $table->dropColumn('sns_subscription_arn');
        });
    }
};
