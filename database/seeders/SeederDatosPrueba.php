<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SeederDatosPrueba extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // ── Usuarios de prueba ──────────────────────────────────────────
        DB::table('USUARIO')->insert([
            [
                'nombre'            => 'Admin',
                'apellido_paterno'  => 'Sistema',
                'apellido_materno'  => '',
                'email'             => 'admin@ito.mx',
                'contrasena'        => Hash::make('admin123'),
                'tipo_usuario'      => 'admin',
                'num_control'       => null,
                'telefono'          => null,
                'ultimo_acceso'     => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'nombre'            => 'Ana',
                'apellido_paterno'  => 'López',
                'apellido_materno'  => 'García',
                'email'             => 'alumno@ito.mx',
                'contrasena'        => Hash::make('alumno123'),
                'tipo_usuario'      => 'alumno',
                'num_control'       => 20230001,
                'telefono'          => '9511234567',
                'ultimo_acceso'     => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ]);

        // Asignar roles a los usuarios
        $userAdmin  = \App\Models\User::where('email', 'admin@ito.mx')->first();
        $userAlumno = \App\Models\User::where('email', 'alumno@ito.mx')->first();
        $userAdmin->assignRole('admin');
        $userAlumno->assignRole('alumno');

        // Carreras del Instituto Tecnológico de Oaxaca
        DB::table('carrera')->insert([
            ['nombre' => 'Ingeniería en Sistemas Computacionales'],
            ['nombre' => 'Ingeniería en Gestión Empresarial'],
            ['nombre' => 'Ingeniería Civil'],
            ['nombre' => 'Ingeniería Industrial'],
            ['nombre' => 'Ingeniería Mecánica'],
            ['nombre' => 'Ingeniería Electrónica'],
            ['nombre' => 'Ingeniería Eléctrica'],
            ['nombre' => 'Ingeniería Química'],
            ['nombre' => 'Licenciatura en Administración'],
            ['nombre' => 'Licenciatura en Contaduría'],
        ]);

        // Departamentos
        DB::table('departamento')->insert([
            ['nombre' => 'Ciencias Básicas', 'edificio' => 'Edificio A'],
            ['nombre' => 'Sistemas y Computación', 'edificio' => 'Edificio B'],
            ['nombre' => 'Ciencias Económico Administrativas', 'edificio' => 'Edificio C'],
            ['nombre' => 'Cultura Física', 'edificio' => 'Gimnasio'],
        ]);

        // Días de la semana
        DB::table('dia_semana')->insert([
            ['nombre_dia' => 'lunes'],
            ['nombre_dia' => 'martes'],
            ['nombre_dia' => 'miercoles'],
            ['nombre_dia' => 'jueves'],
            ['nombre_dia' => 'viernes'],
            ['nombre_dia' => 'sabado'],
        ]);

        // Ubicaciones
        DB::table('ubicacion')->insert([
            ['espacio' => 'Aula 101', 'capacidad' => 30],
            ['espacio' => 'Aula 202', 'capacidad' => 25],
            ['espacio' => 'Gimnasio', 'capacidad' => 50],
            ['espacio' => 'Sala de Cómputo 1', 'capacidad' => 20],
            ['espacio' => 'Virtual', 'capacidad' => 100],
        ]);

        // Semestre activo
        DB::table('semestre')->insert([
            [
                'año'                        => 2026,
                'periodo'                    => 1,
                'fecha_inicio'               => '2026-01-15',
                'fecha_fin'                  => '2026-06-15',
                'fecha_inicio_inscripciones' => '2026-01-01',
                'fecha_fin_inscripciones'    => '2026-02-01',
            ],
        ]);

        // Actividades complementarias
        DB::table('actividad_complementaria')->insert([
            [
                'nombre'          => 'Programación Web con Laravel',
                'descripcion'     => 'Aprende a desarrollar aplicaciones web modernas usando el framework Laravel de PHP.',
                'id_categoria'    => null,
                'id_departamento' => 2,
                'requisitos'      => 'Conocimientos básicos de PHP y HTML',
                'nivel_actividad' => 'Intermedio',
                'disponible'      => true,
                'creditos'        => 2,
            ],
            [
                'nombre'          => 'Inglés Técnico',
                'descripcion'     => 'Curso de inglés enfocado en vocabulario técnico para ingenieros.',
                'id_categoria'    => null,
                'id_departamento' => 1,
                'requisitos'      => 'Ninguno',
                'nivel_actividad' => 'Básico',
                'disponible'      => true,
                'creditos'        => 1,
            ],
            [
                'nombre'          => 'Fútbol Soccer',
                'descripcion'     => 'Actividad deportiva de fútbol soccer para el desarrollo físico y trabajo en equipo.',
                'id_categoria'    => null,
                'id_departamento' => 4,
                'requisitos'      => 'Ninguno',
                'nivel_actividad' => 'Básico',
                'disponible'      => true,
                'creditos'        => 1,
            ],
            [
                'nombre'          => 'Emprendimiento e Innovación',
                'descripcion'     => 'Desarrolla habilidades para crear y gestionar tu propio negocio.',
                'id_categoria'    => null,
                'id_departamento' => 3,
                'requisitos'      => 'Ninguno',
                'nivel_actividad' => 'Básico',
                'disponible'      => true,
                'creditos'        => 2,
            ],
            [
                'nombre'          => 'Diseño Gráfico Digital',
                'descripcion'     => 'Aprende herramientas de diseño gráfico para crear contenido visual profesional.',
                'id_categoria'    => null,
                'id_departamento' => 2,
                'requisitos'      => 'Ninguno',
                'nivel_actividad' => 'Básico',
                'disponible'      => true,
                'creditos'        => 1,
            ],
        ]);

        // Actividad-Carrera (todas las carreras pueden acceder a todas las actividades)
        foreach (range(1, 5) as $actividad) {
            foreach (range(1, 10) as $carrera) {
                DB::table('ACTIVIDAD_CARRERA')->insert([
                    'id_actividad' => $actividad,
                    'id_carrera'   => $carrera,
                ]);
            }
        }

        // Grupos
        DB::table('grupo')->insert([
            // Actividad 1 - Programación Web con Laravel
            [
                'id_actividad'          => 1,
                'id_semestre'           => 1,
                'id_instructor'         => null,
                'id_ubicacion'          => 4,
                'grupo'                 => 'A',
                'cupo_maximo'           => 20,
                'cupo_ocupado'          => 5,
                'modalidad'             => 'presencial',
                'materiales_requeridos' => 'Laptop',
                'estatus'               => 'abierta',
                'fecha_inicio'          => '2026-02-01',
                'fecha_fin'             => '2026-05-31',
            ],
            // Actividad 2 - Inglés Técnico
            [
                'id_actividad'          => 2,
                'id_semestre'           => 1,
                'id_instructor'         => null,
                'id_ubicacion'          => 1,
                'grupo'                 => 'A',
                'cupo_maximo'           => 25,
                'cupo_ocupado'          => 8,
                'modalidad'             => 'presencial',
                'materiales_requeridos' => 'Ninguno',
                'estatus'               => 'abierta',
                'fecha_inicio'          => '2026-02-01',
                'fecha_fin'             => '2026-05-31',
            ],
            // Actividad 3 - Fútbol Soccer
            [
                'id_actividad'          => 3,
                'id_semestre'           => 1,
                'id_instructor'         => null,
                'id_ubicacion'          => 3,
                'grupo'                 => 'A',
                'cupo_maximo'           => 30,
                'cupo_ocupado'          => 15,
                'modalidad'             => 'presencial',
                'materiales_requeridos' => 'Ropa deportiva',
                'estatus'               => 'abierta',
                'fecha_inicio'          => '2026-02-01',
                'fecha_fin'             => '2026-05-31',
            ],
            // Actividad 4 - Emprendimiento e Innovación
            [
                'id_actividad'          => 4,
                'id_semestre'           => 1,
                'id_instructor'         => null,
                'id_ubicacion'          => 2,
                'grupo'                 => 'A',
                'cupo_maximo'           => 20,
                'cupo_ocupado'          => 3,
                'modalidad'             => 'presencial',
                'materiales_requeridos' => 'Cuaderno',
                'estatus'               => 'abierta',
                'fecha_inicio'          => '2026-02-01',
                'fecha_fin'             => '2026-05-31',
            ],
            // Actividad 5 - Diseño Gráfico Digital
            [
                'id_actividad'          => 5,
                'id_semestre'           => 1,
                'id_instructor'         => null,
                'id_ubicacion'          => 5,
                'grupo'                 => 'A',
                'cupo_maximo'           => 15,
                'cupo_ocupado'          => 0,
                'modalidad'             => 'virtual',
                'materiales_requeridos' => 'Computadora',
                'estatus'               => 'abierta',
                'fecha_inicio'          => '2026-02-01',
                'fecha_fin'             => '2026-05-31',
            ],
        ]);

        // Horarios
        DB::table('horario')->insert([
            // Grupo 1 - Programación Web (Lunes y Miércoles)
            ['id_grupo' => 1, 'id_dia' => 1, 'hora_inicio' => '10:00:00', 'hora_fin' => '12:00:00'],
            ['id_grupo' => 1, 'id_dia' => 3, 'hora_inicio' => '10:00:00', 'hora_fin' => '12:00:00'],
            // Grupo 2 - Inglés Técnico (Martes y Jueves)
            ['id_grupo' => 2, 'id_dia' => 2, 'hora_inicio' => '08:00:00', 'hora_fin' => '10:00:00'],
            ['id_grupo' => 2, 'id_dia' => 4, 'hora_inicio' => '08:00:00', 'hora_fin' => '10:00:00'],
            // Grupo 3 - Fútbol Soccer (Viernes)
            ['id_grupo' => 3, 'id_dia' => 5, 'hora_inicio' => '14:00:00', 'hora_fin' => '16:00:00'],
            // Grupo 4 - Emprendimiento (Lunes y Viernes)
            ['id_grupo' => 4, 'id_dia' => 1, 'hora_inicio' => '12:00:00', 'hora_fin' => '14:00:00'],
            ['id_grupo' => 4, 'id_dia' => 5, 'hora_inicio' => '12:00:00', 'hora_fin' => '14:00:00'],
            // Grupo 5 - Diseño Gráfico (Miércoles y Viernes)
            ['id_grupo' => 5, 'id_dia' => 3, 'hora_inicio' => '16:00:00', 'hora_fin' => '18:00:00'],
            ['id_grupo' => 5, 'id_dia' => 5, 'hora_inicio' => '16:00:00', 'hora_fin' => '18:00:00'],
        ]);

        // Registro de alumno (vinculado al usuario alumno@ito.mx)
        $userAlumno = \App\Models\User::where('email', 'alumno@ito.mx')->first();
        DB::table('alumno')->insert([
            [
                'id_alumno'          => $userAlumno->id,
                'id_carrera'         => 1,
                'semestre_cursando'  => 5,
                'creditos_acumulados'=> 0,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
        ]);

        // ── Usuarios instructores ───────────────────────────────────────
        DB::table('USUARIO')->insert([
            [
                'nombre'           => 'Carlos',
                'apellido_paterno' => 'Ramírez',
                'apellido_materno' => 'Mendoza',
                'email'            => 'instructor1@ito.mx',
                'contrasena'       => Hash::make('instructor123'),
                'tipo_usuario'     => 'instructor',
                'num_control'      => null,
                'telefono'         => '9519876543',
                'ultimo_acceso'    => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'nombre'           => 'María',
                'apellido_paterno' => 'Torres',
                'apellido_materno' => 'Vega',
                'email'            => 'instructor2@ito.mx',
                'contrasena'       => Hash::make('instructor123'),
                'tipo_usuario'     => 'instructor',
                'num_control'      => null,
                'telefono'         => '9514567890',
                'ultimo_acceso'    => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'nombre'           => 'Jorge',
                'apellido_paterno' => 'Hernández',
                'apellido_materno' => 'Cruz',
                'email'            => 'instructor3@ito.mx',
                'contrasena'       => Hash::make('instructor123'),
                'tipo_usuario'     => 'instructor',
                'num_control'      => null,
                'telefono'         => '9512345678',
                'ultimo_acceso'    => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);

        // Asignar rol instructor y registrar en tabla instructor
        $inst1 = \App\Models\User::where('email', 'instructor1@ito.mx')->first();
        $inst2 = \App\Models\User::where('email', 'instructor2@ito.mx')->first();
        $inst3 = \App\Models\User::where('email', 'instructor3@ito.mx')->first();

        $inst1->assignRole('instructor');
        $inst2->assignRole('instructor');
        $inst3->assignRole('instructor');

        DB::table('instructor')->insert([
            [
                'id_instructor'  => $inst1->id,
                'id_departamento'=> 2,
                'especialidad'   => 'Desarrollo Web',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'id_instructor'  => $inst2->id,
                'id_departamento'=> 1,
                'especialidad'   => 'Idiomas',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'id_instructor'  => $inst3->id,
                'id_departamento'=> 4,
                'especialidad'   => 'Cultura Física',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}