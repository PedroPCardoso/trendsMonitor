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
        Schema::create('trends', function (Blueprint $table) {
            $table->id();
            $table->string('platform')->index(); // youtube, tiktok, instagram, google
            $table->string('identifier'); // external ID
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->json('metadata')->nullable(); // counts, tags, etc
            $table->integer('rank')->default(0);
            $table->timestamp('fetched_at')->index();
            $table->timestamps();

            $table->index(['platform', 'fetched_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trends');
    }
};
