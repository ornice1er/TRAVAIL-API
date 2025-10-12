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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('action_name');
            $table->longText('description');
            $table->unsignedBigInteger('done_by')->nullable();
            $table->enum('origin', ['AUTH', 'EDITION', 'ACTIVITE', 'RAPPORT']);
            $table->timestamps();
            
            // Index pour améliorer les performances
            $table->index('done_by');
            $table->index('origin');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
