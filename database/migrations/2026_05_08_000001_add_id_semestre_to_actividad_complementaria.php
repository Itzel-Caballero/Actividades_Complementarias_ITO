<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdSemestreToActividadComplementaria extends Migration
{
    public function up(): void
    {
        Schema::table('actividad_complementaria', function (Blueprint $table) {
            $table->unsignedBigInteger('id_semestre')->nullable()->after('id_departamento');
            $table->foreign('id_semestre')->references('id_semestre')->on('semestre')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('actividad_complementaria', function (Blueprint $table) {
            $table->dropForeign(['id_semestre']);
            $table->dropColumn('id_semestre');
        });
    }
}
