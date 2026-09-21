<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rsvps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            // Set when a signed-in user responds; guests reply anonymously.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->string('status')->default('attending'); // attending | not_attending
            $table->unsignedSmallInteger('party_size')->default(1);

            $table->string('meal_preference')->nullable();  // veg | non_veg
            $table->boolean('needs_accommodation')->default(false);
            $table->text('note')->nullable();

            $table->timestamps();

            // One response per email per event, so re-submitting updates rather
            // than piling up duplicates. Nullable emails are exempt in MySQL.
            $table->unique(['event_id', 'email']);
            $table->index(['event_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rsvps');
    }
};
