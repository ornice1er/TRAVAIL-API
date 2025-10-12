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
        Schema::create('fiche_metiers', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('resume');
            $table->text('description');
            $table->unsignedBigInteger('structure_id'); 
            $table->json('thematique'); 
            $table->timestamps();
            
           
            $table->index('structure_id');
        });
        
       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiche_metiers');
    }
};