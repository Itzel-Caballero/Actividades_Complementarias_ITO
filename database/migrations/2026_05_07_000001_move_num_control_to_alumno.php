<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MoveNumControlToAlumno extends Migration
{
    public function up(): void
    {
        // 1. Agregar num_control a la tabla alumno (varchar 9)
        Schema::table('alumno', function (Blueprint $table) {
            $table->string('num_control', 9)->nullable()->unique()->after('id_alumno');
        });

        // 2. Migrar datos existentes de USUARIO.num_control -> alumno.num_control
        \DB::statement('
            UPDATE alumno
            INNER JOIN USUARIO ON alumno.id_alumno = USUARIO.id
            SET alumno.num_control = CAST(USUARIO.num_control AS CHAR(9))
            WHERE USUARIO.num_control IS NOT NULL
        ');

        // 3. Eliminar la columna num_control de USUARIO
        Schema::table('USUARIO', function (Blueprint $table) {
            $table->dropColumn('num_control');
        });
    }

    public function down(): void
    {
        // Revertir: devolver num_control a USUARIO
        Schema::table('USUARIO', function (Blueprint $table) {
            $table->string('num_control', 9)->nullable()->after('tipo_usuario');
        });

        // Restaurar datos
        \DB::statement('
            UPDATE USUARIO
            INNER JOIN alumno ON alumno.id_alumno = USUARIO.id
            SET USUARIO.num_control = alumno.num_control
            WHERE alumno.num_control IS NOT NULL
        ');

        // Quitar num_control de alumno
        Schema::table('alumno', function (Blueprint $table) {
            $table->dropColumn('num_control');
        });
    }
}
