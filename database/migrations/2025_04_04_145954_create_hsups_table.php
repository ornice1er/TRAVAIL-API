<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute les migrations.
     */
    public function up(): void
    {
        Schema::create('hsups', function (Blueprint $table) {
            $table->id();
            $table->integer('agent_id');
            $table->string('type');
            $table->string('nbHeure');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Annule les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hsups');
    }
};
