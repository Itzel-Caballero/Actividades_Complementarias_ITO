<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
{
    Schema::create('horario', function (Blueprint $table) {
        $table->id('id_horario');
        $table->unsignedBigInteger('id_grupo');
        $table->unsignedBigInteger('id_dia');
        $table->time('hora_inicio');
        $table->time('hora_fin');
        $table->timestamps();

        $table->foreign('id_grupo')->references('id_grupo')->on('grupo');
        $table->foreign('id_dia')->references('id_dia')->on('dia_semana');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('horario');
    }
}
