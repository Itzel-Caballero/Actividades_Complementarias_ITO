<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE USUARIO CHANGE id_usuario id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE USUARIO CHANGE id id_usuario BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
    }
};