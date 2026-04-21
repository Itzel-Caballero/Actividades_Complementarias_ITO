<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAlumnoTable extends Migration
{
    public function up(): void
    {
        // Eliminamos la tabla si existe para evitar conflictos
        Schema::dropIfExists('alumno');

        Schema::create('alumno', function (Blueprint $table) {
            // Definimos id_alumno como la llave primaria y foránea al mismo tiempo
            $table->unsignedBigInteger('id_alumno')->primary();
            $table->unsignedBigInteger('id_carrera');
            $table->integer('semestre_cursando');
            $table->integer('creditos_acumulados')->default(0);
            $table->timestamps();

            // Definimos la relación aquí mismo de forma fluida
            $table->foreign('id_alumno')
                  ->references('id')
                  ->on('USUARIO') // La tabla se llama 'USUARIO' (en mayúsculas)
                  ->onDelete('cascade'); // <--- ESTO ELIMINA AL ALUMNO CUANDO BORRES AL USUARIO
        });

        // Nota: He comentado el DB::statement porque la línea de arriba ya hace el trabajo
        // DB::statement('ALTER TABLE alumno ADD CONSTRAINT alumno_usuario_fk FOREIGN KEY (id_alumno) REFERENCES USUARIO(id)');
    }

    public function down()
    {
        Schema::dropIfExists('alumno');
    }
}