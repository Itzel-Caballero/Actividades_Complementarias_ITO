<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('USUARIO', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('email', 100)->unique();
            $table->string('contraseña', 255);
            $table->enum('tipo_usuario', ['alumno', 'instructor', 'admin', 'coordinador']);
            $table->dateTime('ultimo_acceso')->nullable();
            $table->integer('num_control')->nullable();
            $table->string('nombre', 100)->nullable();
            $table->string('apellido_paterno', 100)->nullable();
            $table->string('apellido_materno', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('USUARIO');
    }
}
