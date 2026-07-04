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
        Schema::table('gift_addons', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description');
        });

        Schema::table('order_addons', function (Blueprint $table) {
            $table->string('image')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gift_addons', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('order_addons', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
