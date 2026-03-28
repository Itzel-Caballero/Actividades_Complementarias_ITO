<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SeederUsuarios extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // ── Admin ──────────────────────────────────────────────────────
        $admin = User::create([
            'nombre'           => 'Administrador',
            'apellido_paterno' => 'Sistema',
            'apellido_materno' => 'ITO',
            'email'            => 'admin@gmail.com',
            'contrasena'       => Hash::make('123456'),
            'tipo_usuario'     => 'admin',
            'num_control'      => null,
            'telefono'         => '9511000001',
            'ultimo_acceso'    => null,
        ]);
        $admin->assignRole('admin');

        // ── Coordinador ────────────────────────────────────────────────
        $coord = User::create([
            'nombre'           => 'Coordinador',
            'apellido_paterno' => 'Actividades',
            'apellido_materno' => 'Complementarias',
            'email'            => 'cord@gmail.com',
            'contrasena'       => Hash::make('123456'),
            'tipo_usuario'     => 'coordinador',
            'num_control'      => null,
            'telefono'         => '9511000002',
            'ultimo_acceso'    => null,
        ]);
        $coord->assignRole('coordinador');

        // ── Maestro / Instructor ───────────────────────────────────────
        $maestro = User::create([
            'nombre'           => 'Carlos',
            'apellido_paterno' => 'García',
            'apellido_materno' => 'López',
            'email'            => 'maestro@gmail.com',
            'contrasena'       => Hash::make('123456'),
            'tipo_usuario'     => 'instructor',
            'num_control'      => null,
            'telefono'         => '9511000003',
            'ultimo_acceso'    => null,
        ]);
        $maestro->assignRole('instructor');

        // Registrar en tabla instructor (necesita al menos 1 departamento)
        $depto = DB::table('departamento')->first();
        if ($depto) {
            DB::table('instructor')->insert([
                'id_instructor'   => $maestro->id,
                'id_departamento' => $depto->id_departamento,
                'especialidad'    => 'Actividades Físicas',
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        // ── Alumno ─────────────────────────────────────────────────────
        $alumno = User::create([
            'nombre'           => 'Ana',
            'apellido_paterno' => 'Martínez',
            'apellido_materno' => 'Ruiz',
            'email'            => 'alumno@gmail.com',
            'contrasena'       => Hash::make('123456'),
            'tipo_usuario'     => 'alumno',
            'num_control'      => '20230001',
            'telefono'         => '9511000004',
            'ultimo_acceso'    => null,
        ]);
        $alumno->assignRole('alumno');

        // Registrar en tabla alumno
        $carrera = DB::table('carrera')->first();
        if ($carrera) {
            DB::table('alumno')->insert([
                'id_alumno'           => $alumno->id,
                'id_carrera'          => $carrera->id_carrera,
                'semestre_cursando'   => 5,
                'creditos_acumulados' => 0,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
