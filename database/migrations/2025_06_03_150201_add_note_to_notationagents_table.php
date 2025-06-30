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
        Schema::table('notationagents', function (Blueprint $table) {
            //
             $table->decimal('note', 10, 5)->after('periode_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notationagents', function (Blueprint $table) {
            //
             $table->dropColumn('note');
        });
    }
};
