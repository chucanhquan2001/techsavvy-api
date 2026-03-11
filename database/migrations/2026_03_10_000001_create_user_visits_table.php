<?php

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
        if (! Schema::hasTable('user_visits')) {
            Schema::create('user_visits', function (Blueprint $table) {
                $table->id();
                $table->string('ip_address', 45);        // IPv4/IPv6
                $table->string('user_agent')->nullable();
                $table->string('referer')->nullable();
                $table->string('full_url')->nullable();
                $table->string('device_type')->nullable();    // mobile/desktop/tablet
                $table->string('browser')->nullable();         // chrome/firefox/safari
                $table->string('browser_version')->nullable();
                $table->string('platform')->nullable();        // windows/macos/linux/ios/android
                $table->string('platform_version')->nullable();
                $table->string('country_code', 2)->nullable(); // VN/US/...
                $table->string('country_name')->nullable();
                $table->string('fbclid')->nullable();           // Facebook Click ID
                $table->string('gclid')->nullable();            // Google Click ID
                $table->string('utm_source')->nullable();
                $table->string('utm_medium')->nullable();
                $table->string('utm_campaign')->nullable();
                $table->string('utm_content')->nullable();
                $table->string('utm_term')->nullable();
                $table->timestamps();

                // Index for better query performance
                $table->index('ip_address');
                $table->index('created_at');
                $table->index('fbclid');
                $table->index('gclid');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_visits');
    }
};
