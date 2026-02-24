<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_action_settings', function (Blueprint $table) {
            $table->json('weekly_off_days')->nullable()->after('next_execution_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_action_settings', function (Blueprint $table) {
            $table->dropColumn('weekly_off_days');
        });
    }
};
