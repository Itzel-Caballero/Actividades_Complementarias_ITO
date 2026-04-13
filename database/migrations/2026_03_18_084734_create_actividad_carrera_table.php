<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadCarreraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
{
    Schema::create('actividad_carrera', function (Blueprint $table) {
        $table->unsignedBigInteger('id_actividad');
        $table->unsignedBigInteger('id_carrera');
        $table->primary(['id_actividad', 'id_carrera']);

        $table->foreign('id_actividad')->references('id_actividad')->on('actividad_complementaria');
        $table->foreign('id_carrera')->references('id_carrera')->on('carrera');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actividad_carrera');
    }
}
