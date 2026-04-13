<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAlumnoTable extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('alumno');

        Schema::create('alumno', function (Blueprint $table) {
            $table->integer('id_alumno')->primary();
            $table->unsignedBigInteger('id_carrera');
            $table->integer('semestre_cursando');
            $table->integer('creditos_acumulados')->default(0);
            $table->timestamps();

            $table->foreign('id_carrera')->references('id_carrera')->on('carrera');
        });

        DB::statement('ALTER TABLE alumno ADD CONSTRAINT alumno_usuario_fk FOREIGN KEY (id_alumno) REFERENCES USUARIO(id)');
    }

    public function down()
    {
        Schema::dropIfExists('alumno');
    }
}
