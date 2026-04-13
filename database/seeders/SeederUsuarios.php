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

        // ── 100 alumnos de prueba ─────────────────────────────────────────────
        $nombres = [
            'Alejandro','Brenda','Carlos','Diana','Eduardo','Fernanda','Gabriel','Hilda',
            'Iván','Juana','Kevin','Laura','Miguel','Ángela','Omar','Patricia','Quirino',
            'Rosa','Salvador','Teresa','Ulises','Valeria','Waldo','Ximena','Yair','Zara',
            'Arturo','Beatriz','César','Dalia','Ernesto','Flor','Gerardo','Helena',
            'Isaac','Judith','Luis','María','Nicolás','Olga','Pablo','Rebeca','Santiago',
            'Tania','Uriel','Verónica','Xavier','Yolanda','Zaira','Alfredo',
        ];
        $apellidos_p = [
            'García','López','Martínez','Sánchez','Rodríguez','Pérez','González','Hernández',
            'Jiménez','Flores','Díaz','Torres','Ramírez','Morales','Reyes','Cruz','Mendoza',
            'Ortiz','Castillo','Guerrero','Vargas','Ramos','Alvarado','Cervantes','Soto',
        ];
        $apellidos_m = [
            'Luna','Vega','Castro','Salinas','Fuentes','Escobar','Mora','Tapia',
            'Guérrero','Nava','Bravo','Ibarra','Palma','Delgado','Montes','Avila',
            'Rios','Cabrera','Herrera','Medina','Ponce','Aguilar','Bernal','Cisneros',
        ];

        $carreras = DB::table('carrera')->pluck('id_carrera')->toArray();
        $semestres = [1,2,3,4,5,6,7,8,9];
        $role = \Spatie\Permission\Models\Role::findByName('alumno');

        for ($i = 1; $i <= 100; $i++) {
            $nombre    = $nombres[array_rand($nombres)];
            $ap        = $apellidos_p[array_rand($apellidos_p)];
            $am        = $apellidos_m[array_rand($apellidos_m)];
            $numCtrl   = '2024' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $email     = 'alumno' . str_pad($i, 3, '0', STR_PAD_LEFT) . '@gmail.com';
            $idCarrera = $carreras[array_rand($carreras)];
            $semestre  = $semestres[array_rand($semestres)];

            $nuevoAlumno = User::create([
                'nombre'           => $nombre,
                'apellido_paterno' => $ap,
                'apellido_materno' => $am,
                'email'            => $email,
                'contrasena'       => Hash::make('123456'),
                'tipo_usuario'     => 'alumno',
                'num_control'      => $numCtrl,
                'telefono'         => '951' . rand(1000000, 9999999),
                'ultimo_acceso'    => null,
            ]);

            $nuevoAlumno->assignRole('alumno');

            DB::table('alumno')->insert([
                'id_alumno'           => $nuevoAlumno->id,
                'id_carrera'          => $idCarrera,
                'semestre_cursando'   => $semestre,
                'creditos_acumulados' => rand(0, 10),
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
