<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            SeederTablaPermisos::class,
            SeederTablaRoles::class,
            SeederDatosPrueba::class,      // datos base: carreras, deptos, días, semestres, actividades
            SeederUsuarios::class,          // admin@gmail.com, cord@gmail.com, maestro@gmail.com, alumno@gmail.com
            SeederInstructores::class,      // 5 instructores adicionales con correos @ito.mx
        ]);
    }
}
