<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddUniqueActiveInscripcion extends Migration
{
    /**
     * Run the migrations.
     * Crea un índice único parcial (filtered) para que un alumno solo pueda tener
     * una inscripción activa (inscrito o cursando) a la vez.
     *
     * Nota: MySQL no soporta índices únicos parciales/filtrados de forma nativa.
     * Usamos un índice único compuesto sobre (id_alumno, estatus) donde estatus
     * se limita a los valores activos. Como alternativa, usamos un enfoque
     * híbrido: creamos una columna virtual que es NULL cuando el estatus NO es
     * activo, y el id_alumno cuando SÍ es activo. Luego un índice único sobre
     * esa columna virtual.
     */
    public function up(): void
    {
        // Para MySQL, creamos una columna virtual generada que almacena id_alumno
        // solo cuando el estatus es 'inscrito' o 'cursando', y NULL en otro caso.
        // Luego creamos un índice único sobre esa columna.
        DB::statement("
            ALTER TABLE inscripcion
            ADD COLUMN unique_active_alumno INT AS (
                CASE WHEN estatus IN ('inscrito', 'cursando') THEN id_alumno ELSE NULL END
            ) STORED,
            ADD UNIQUE INDEX uq_inscripcion_activa (unique_active_alumno)
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE inscripcion DROP INDEX uq_inscripcion_activa");
        DB::statement("ALTER TABLE inscripcion DROP COLUMN unique_active_alumno");
    }
}
