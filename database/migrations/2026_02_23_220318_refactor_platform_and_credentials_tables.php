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
            $table->text('authentication_curl_template')->nullable()->after('icon');
            $table->string('auth_token_key')->nullable()->after('authentication_curl_template');
        });

        Schema::table('user_platform_credentials', function (Blueprint $table) {
            $table->dropColumn(['authentication_curl', 'auth_token_key']);

            $table->string('username')->after('platform_id');
            $table->text('password')->after('username'); // Encrypted
            $table->string('location')->nullable()->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->dropColumn(['authentication_curl_template', 'auth_token_key']);
        });

        Schema::table('user_platform_credentials', function (Blueprint $table) {
            $table->dropColumn(['username', 'password', 'location']);

            $table->text('authentication_curl');
            $table->string('auth_token_key')->nullable();
        });
    }
};
