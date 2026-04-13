<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('market_price_snapshots')) {
            Schema::create('market_price_snapshots', function (Blueprint $table) {
                $table->id();
                $table->string('instrument');
                $table->string('category');
                $table->decimal('value', 20, 8);
                $table->string('currency', 10);
                $table->string('unit', 32)->nullable();
                $table->timestamp('quoted_at');
                $table->string('source');
                $table->string('source_type', 16);
                $table->timestamps();

                $table->unique(['instrument', 'source']);
                $table->index(['category', 'instrument']);
                $table->index('quoted_at');
            });
        }

        if (! Schema::hasTable('market_price_histories')) {
            Schema::create('market_price_histories', function (Blueprint $table) {
                $table->id();
                $table->string('instrument');
                $table->string('category');
                $table->decimal('value', 20, 8);
                $table->string('currency', 10);
                $table->string('unit', 32)->nullable();
                $table->timestamp('quoted_at');
                $table->string('source');
                $table->string('source_type', 16);
                $table->timestamps();

                $table->index(['instrument', 'quoted_at']);
                $table->index(['category', 'quoted_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('market_price_histories');
        Schema::dropIfExists('market_price_snapshots');
    }
};
