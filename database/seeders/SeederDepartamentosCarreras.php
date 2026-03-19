<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeederDepartamentosCarreras extends Seeder
{
    public function run(): void
    {
        // Limpiar tablas primero
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('actividad_carrera')->truncate();
        DB::table('carrera')->truncate();
        DB::table('departamento')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Departamentos
        DB::table('departamento')->insert([
            ['nombre' => 'Ing. en Sistemas Computacionales',  'edificio' => null],
            ['nombre' => 'Ing. Electrónica',                  'edificio' => null],
            ['nombre' => 'Ing. Química y Bioquímica',         'edificio' => null],
            ['nombre' => 'Centro de Información',             'edificio' => null],
            ['nombre' => 'Ing. Eléctrica',                    'edificio' => null],
            ['nombre' => 'Ing. Industrial',                   'edificio' => null],
            ['nombre' => 'Ciencias de la Tierra',             'edificio' => null],
            ['nombre' => 'Desarrollo Académico',              'edificio' => null],
            ['nombre' => 'Ciencias Básicas',                  'edificio' => null],
            ['nombre' => 'Actividades Extraescolares',        'edificio' => null],
        ]);

        // Carreras
        DB::table('carrera')->insert([
            ['nombre' => 'Ing. en Sistemas Computacionales'],
            ['nombre' => 'Ing. en Gestión Empresarial'],
            ['nombre' => 'Ing. Química'],
            ['nombre' => 'Ing. Civil'],
            ['nombre' => 'Ing. Electrónica'],
            ['nombre' => 'Ing. Eléctrica'],
            ['nombre' => 'Lic. Contador Público'],
            ['nombre' => 'Administración'],
        ]);
    }
}