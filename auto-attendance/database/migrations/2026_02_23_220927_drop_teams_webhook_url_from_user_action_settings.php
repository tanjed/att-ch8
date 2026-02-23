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
            $table->dropColumn('teams_webhook_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_action_settings', function (Blueprint $table) {
            $table->string('teams_webhook_url')->nullable();
        });
    }
};
