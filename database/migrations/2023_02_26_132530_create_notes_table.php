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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('updater_id')->constrained('users');
            $table->foreignId('category_id')->constrained();
            $table->string('title')->nullable();
            $table->string('slug')->unique()->nullable();
            $table->text('content')->fullText()->nullable();
            $table->text('plaintext')->fullText()->nullable();
            $table->enum('type', ['Article', 'Note'])->default('Article');
            $table->enum('locale', ['FI', 'EN', 'SE'])->default('FI');
            $table->boolean('is_published')->default(True);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
