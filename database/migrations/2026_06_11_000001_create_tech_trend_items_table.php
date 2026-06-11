<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tech_trend_items')) {
            Schema::create('tech_trend_items', function (Blueprint $table) {
                $table->id();
                $table->string('type', 32);
                $table->string('source', 64);
                $table->string('source_url', 1024);
                $table->string('external_id', 512);
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('url', 1024);
                $table->text('description')->nullable();
                $table->mediumText('readme_excerpt')->nullable();
                $table->text('summary')->nullable();
                $table->mediumText('how_it_works')->nullable();
                $table->json('technologies')->nullable();
                $table->string('language', 64)->nullable();
                $table->json('topics')->nullable();
                $table->unsignedBigInteger('stars')->nullable();
                $table->unsignedBigInteger('forks')->nullable();
                $table->unsignedInteger('stars_today')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->date('trend_date');
                $table->json('raw_payload')->nullable();
                $table->string('content_hash', 64);
                $table->string('status', 32)->default('ready');
                $table->timestamps();

                $table->unique(['source', 'external_id', 'trend_date']);
                $table->index(['type', 'trend_date']);
                $table->index(['source', 'trend_date']);
                $table->index(['status', 'trend_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tech_trend_items');
    }
};
