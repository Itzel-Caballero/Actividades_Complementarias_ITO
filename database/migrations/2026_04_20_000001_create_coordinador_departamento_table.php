<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoordinadorDepartamentoTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('coordinador_departamento');
        
        Schema::create('coordinador_departamento', function (Blueprint $table) {
            $table->id();
            // Un solo coordinador por departamento (unique en id_departamento)
            $table->unsignedBigInteger('id_usuario')->comment('FK a USUARIO (debe tener rol coordinador)');
            $table->unsignedBigInteger('id_departamento')->unique()->comment('Cada departamento solo tiene un coordinador');
            $table->timestamps();

            $table->foreign('id_usuario')
                  ->references('id')
                  ->on('USUARIO')
                  ->onDelete('cascade');

            $table->foreign('id_departamento')
                  ->references('id_departamento')
                  ->on('departamento')
                  ->onDelete('cascade');

            // Un usuario solo puede coordinador un departamento a la vez
            $table->unique('id_usuario');
        });
    }

    public function down()
    {
        Schema::dropIfExists('coordinador_departamento');
    }
}
