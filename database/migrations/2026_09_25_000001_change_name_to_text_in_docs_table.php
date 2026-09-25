<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Les intitulés de documents (arrêtés, décrets...) dépassent souvent 191 caractères.
     * Requête brute : doctrine/dbal n'est pas installé, ->change() n'est donc pas disponible.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE `docs` MODIFY `name` TEXT NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `docs` MODIFY `name` VARCHAR(191) NOT NULL');
    }
};
