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
        Schema::table('events', function (Blueprint $table) {
            $table->string('template')->default('classic')->after('cover_photo_url');
            $table->string('invitation_template')->default('elegant')->after('template');
            $table->json('template_data')->nullable()->after('invitation_template');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['template', 'invitation_template', 'template_data']);
        });
    }
};
