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
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->string('link');
            $table->foreignId('user_id')
            ->references('id')
            ->on('users') 
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->foreignId('file_id')
            ->references('id')
            ->on('files') 
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->string('description')->default("");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
