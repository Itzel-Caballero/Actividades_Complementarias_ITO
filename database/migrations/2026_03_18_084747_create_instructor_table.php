<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateInstructorTable extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('instructor');

        Schema::create('instructor', function (Blueprint $table) {
            $table->unsignedBigInteger('id_instructor')->primary();
            $table->unsignedBigInteger('id_departamento');
            $table->string('especialidad', 100)->nullable();
            $table->timestamps();

            $table->foreign('id_departamento')->references('id_departamento')->on('departamento');
        });

        DB::statement('ALTER TABLE instructor ADD CONSTRAINT instructor_usuario_fk FOREIGN KEY (id_instructor) REFERENCES USUARIO(id)');
    }

    public function down()
    {
        Schema::dropIfExists('instructor');
    }
}
