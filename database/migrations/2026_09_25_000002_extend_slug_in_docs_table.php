<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Le slug est dérivé du nom, désormais en TEXT : on l'allonge en gardant l'index unique
     * (500 x 4 octets utf8mb4 = 2000 octets, sous la limite InnoDB de 3072 en ROW_FORMAT DYNAMIC).
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE `docs` MODIFY `slug` VARCHAR(500) NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `docs` MODIFY `slug` VARCHAR(191) NOT NULL');
    }
};
