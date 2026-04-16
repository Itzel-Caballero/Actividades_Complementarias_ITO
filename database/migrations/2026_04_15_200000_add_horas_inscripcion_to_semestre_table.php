<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHorasInscripcionToSemestreTable extends Migration
{
    public function up(): void
    {
        Schema::table('semestre', function (Blueprint $table) {
            // Hora de inicio y fin de las inscripciones (HH:MM)
            $table->time('hora_inicio_inscripciones')->nullable()->after('fecha_inicio_inscripciones');
            $table->time('hora_fin_inscripciones')->nullable()->after('fecha_fin_inscripciones');
        });
    }

    public function down(): void
    {
        Schema::table('semestre', function (Blueprint $table) {
            $table->dropColumn(['hora_inicio_inscripciones', 'hora_fin_inscripciones']);
        });
    }
}
