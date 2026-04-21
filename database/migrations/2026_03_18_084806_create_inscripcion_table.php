<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateInscripcionTable extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('inscripcion');

        Schema::create('inscripcion', function (Blueprint $table) {
            $table->id('id_inscripcion');
            $table->unsignedBigInteger('id_alumno');
            $table->unsignedBigInteger('id_grupo');
            $table->datetime('fecha_inscripcion');
            $table->enum('estatus', ['inscrito', 'cursando', 'aprobado', 'reprobado', 'dado_de_baja'])->default('inscrito');
            $table->timestamps();

            $table->foreign('id_grupo')->references('id_grupo')->on('grupo');
        });

        DB::statement('ALTER TABLE inscripcion ADD CONSTRAINT inscripcion_alumno_fk FOREIGN KEY (id_alumno) REFERENCES alumno(id_alumno)');
    }

    public function down()
    {
        Schema::dropIfExists('inscripcion');
    }
}
