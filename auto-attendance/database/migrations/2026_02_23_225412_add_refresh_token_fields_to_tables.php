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
        Schema::table('platforms', function (Blueprint $table) {
            $table->text('refresh_curl_template')->nullable()->after('authentication_curl_template');
            $table->string('refresh_token_key')->nullable()->after('auth_token_key');
        });

        Schema::table('user_platform_credentials', function (Blueprint $table) {
            $table->text('access_token')->nullable()->after('password');
            $table->text('refresh_token')->nullable()->after('access_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->dropColumn(['refresh_curl_template', 'refresh_token_key']);
        });

        Schema::table('user_platform_credentials', function (Blueprint $table) {
            $table->dropColumn(['access_token', 'refresh_token']);
        });
    }
};
