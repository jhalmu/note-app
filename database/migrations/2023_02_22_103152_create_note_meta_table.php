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
        Schema::create('note_meta', function (Blueprint $table) {
            $table->id();
            $table->string('note_id');
            $table->string('og_image')->nullable();
            $table->string('og_title')->nullable();
            $table->string('og_description')->nullable();
            $table->string('twitter_image')->nullable();
            $table->string('twitter_title')->nullable();
            $table->string('twitter_description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('email_subject')->nullable();
            $table->string('frontmatter')->nullable();
            $table->string('feature_image_alt')->nullable();
            $table->string('feature_image_caption')->nullable();
            $table->boolean('email_only');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_meta');
    }
};
