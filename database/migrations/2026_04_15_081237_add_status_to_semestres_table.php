<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToSemestresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('semestre', function (Blueprint $table) {
        // Agregamos la columna status, por defecto 'inactivo'
        $table->string('status')->default('inactivo')->after('fecha_fin_inscripciones');
    });
}

public function down()
{
    Schema::table('semestre', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}
}
