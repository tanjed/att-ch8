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
            $table->integer('buffer_minutes')->default(0)->after('target_time');
            $table->time('next_execution_time')->nullable()->after('buffer_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_action_settings', function (Blueprint $table) {
            $table->dropColumn(['buffer_minutes', 'next_execution_time']);
        });
    }
};
