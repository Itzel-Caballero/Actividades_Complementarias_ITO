<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SeederInstructores extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Obtener IDs de departamentos existentes
        $deptos = DB::table('departamento')->pluck('id_departamento', 'nombre');

        $instructores = [
            [
                'nombre'           => 'Roberto',
                'apellido_paterno' => 'Hernández',
                'apellido_materno' => 'Cruz',
                'email'            => 'rhernandez@ito.mx',
                'telefono'         => '9511001001',
                'especialidad'     => 'Programación y Desarrollo Web',
                'depto_key'        => 0, // primer depto disponible
            ],
            [
                'nombre'           => 'María',
                'apellido_paterno' => 'Torres',
                'apellido_materno' => 'Vega',
                'email'            => 'mtorres@ito.mx',
                'telefono'         => '9511001002',
                'especialidad'     => 'Idiomas y Comunicación',
                'depto_key'        => 0,
            ],
            [
                'nombre'           => 'Jorge',
                'apellido_paterno' => 'Ramírez',
                'apellido_materno' => 'Sánchez',
                'email'            => 'jramirez@ito.mx',
                'telefono'         => '9511001003',
                'especialidad'     => 'Cultura Física y Deportes',
                'depto_key'        => 0,
            ],
            [
                'nombre'           => 'Sofía',
                'apellido_paterno' => 'Morales',
                'apellido_materno' => 'Díaz',
                'email'            => 'smorales@ito.mx',
                'telefono'         => '9511001004',
                'especialidad'     => 'Emprendimiento e Innovación',
                'depto_key'        => 0,
            ],
            [
                'nombre'           => 'Luis',
                'apellido_paterno' => 'Gutiérrez',
                'apellido_materno' => 'Pérez',
                'email'            => 'lgutierrez@ito.mx',
                'telefono'         => '9511001005',
                'especialidad'     => 'Diseño Gráfico y Multimedia',
                'depto_key'        => 0,
            ],
        ];

        $deptoIds = DB::table('departamento')->pluck('id_departamento')->toArray();
        $deptoCount = count($deptoIds);

        foreach ($instructores as $i => $data) {
            // Verificar que el correo no exista
            if (User::where('email', $data['email'])->exists()) {
                continue;
            }

            $user = User::create([
                'nombre'           => $data['nombre'],
                'apellido_paterno' => $data['apellido_paterno'],
                'apellido_materno' => $data['apellido_materno'],
                'email'            => $data['email'],
                'contrasena'       => Hash::make('123456'),
                'tipo_usuario'     => 'instructor',
                'num_control'      => null,
                'telefono'         => $data['telefono'],
                'ultimo_acceso'    => null,
            ]);

            $user->assignRole('instructor');

            // Asignar departamento de forma circular si hay varios
            $deptoId = $deptoCount > 0
                ? $deptoIds[$i % $deptoCount]
                : 1;

            DB::table('instructor')->insert([
                'id_instructor'   => $user->id,
                'id_departamento' => $deptoId,
                'especialidad'    => $data['especialidad'],
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
