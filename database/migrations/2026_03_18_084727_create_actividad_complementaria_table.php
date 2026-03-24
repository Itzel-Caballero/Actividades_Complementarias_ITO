<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadComplementariaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
{
    Schema::create('actividad_complementaria', function (Blueprint $table) {
        $table->id('id_actividad');
        $table->string('nombre', 150);
        $table->text('descripcion')->nullable();
        $table->unsignedBigInteger('id_categoria')->nullable();
        $table->unsignedBigInteger('id_departamento');
        $table->text('requisitos')->nullable();
        $table->string('nivel_actividad', 50)->nullable();
        $table->boolean('disponible')->default(true);
        $table->integer('creditos');
        $table->timestamps();

        $table->foreign('id_departamento')->references('id_departamento')->on('departamento');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actividad_complementaria');
    }
}
