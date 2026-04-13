<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrupoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
{
    Schema::create('grupo', function (Blueprint $table) {
        $table->id('id_grupo');
        $table->unsignedBigInteger('id_actividad');
        $table->unsignedBigInteger('id_semestre');
        $table->unsignedBigInteger('id_instructor')->nullable();
        $table->unsignedBigInteger('id_ubicacion')->nullable();
        $table->string('grupo', 10);
        $table->integer('cupo_maximo');
        $table->integer('cupo_ocupado')->default(0);
        $table->enum('modalidad', ['presencial', 'virtual', 'hibrida']);
        $table->text('materiales_requeridos')->nullable();
        $table->enum('estatus', ['abierta', 'cerrada', 'cancelada', 'finalizada'])->default('abierta');
        $table->date('fecha_inicio');
        $table->date('fecha_fin');
        $table->timestamps();

        $table->foreign('id_actividad')->references('id_actividad')->on('actividad_complementaria');
        $table->foreign('id_semestre')->references('id_semestre')->on('semestre');
        $table->foreign('id_ubicacion')->references('id_ubicacion')->on('ubicacion');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grupo');
    }
}
