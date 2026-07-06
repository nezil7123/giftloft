<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Wishlist items are now sourced only from the catalog. Link each item to
     * its product so we can dedupe adds and, later, reflect catalog changes.
     */
    public function up(): void
    {
        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->foreignId('product_id')
                ->nullable()
                ->after('user_id')
                ->constrained('products')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
        });
    }
};
