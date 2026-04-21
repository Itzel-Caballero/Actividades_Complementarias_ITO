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

        // ── Semestres (periodo 1 = Enero-Junio, periodo 2 = Agosto-Diciembre) ──
        DB::table('semestre')->insert([
            [
                'año'                        => 2026,
                'periodo'                    => 1,   // Enero–Junio 2026
                'fecha_inicio'               => '2026-01-15',
                'fecha_fin'                  => '2026-06-15',
                'fecha_inicio_inscripciones' => '2026-01-01',
                'fecha_fin_inscripciones'    => '2026-02-01',
                'status'                     => 'activo',
                'created_at'                 => now(),
                'updated_at'                 => now(),
            ],
            [
                'año'                        => 2026,
                'periodo'                    => 2,   // Agosto–Diciembre 2026
                'fecha_inicio'               => '2026-08-01',
                'fecha_fin'                  => '2026-12-15',
                'fecha_inicio_inscripciones' => '2026-07-15',
                'fecha_fin_inscripciones'    => '2026-08-15',
                'status'                     => 'inactivo',
                'created_at'                 => now(),
                'updated_at'                 => now(),
            ],
        ]);

        // ── Días de la semana ──────────────────────────────────────────
        DB::table('dia_semana')->insert([
            ['nombre_dia' => 'lunes',     'created_at' => now(), 'updated_at' => now()],
            ['nombre_dia' => 'martes',    'created_at' => now(), 'updated_at' => now()],
            ['nombre_dia' => 'miercoles', 'created_at' => now(), 'updated_at' => now()],
            ['nombre_dia' => 'jueves',    'created_at' => now(), 'updated_at' => now()],
            ['nombre_dia' => 'viernes',   'created_at' => now(), 'updated_at' => now()],
            ['nombre_dia' => 'sabado',    'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── Departamentos ──────────────────────────────────────────────
        DB::table('departamento')->insert([
            ['nombre' => 'Ciencias Básicas',                   'edificio' => 'Edificio A'],
            ['nombre' => 'Sistemas y Computación',             'edificio' => 'Edificio B'],
            ['nombre' => 'Ciencias Económico Administrativas', 'edificio' => 'Edificio C'],
            ['nombre' => 'Cultura Física',                     'edificio' => 'Gimnasio'],
            ['nombre' => 'Ingeniería Eléctrica y Electrónica', 'edificio' => 'Edificio D'],
        ]);

        // ── Carreras ───────────────────────────────────────────────────
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

        // ── Ubicaciones ────────────────────────────────────────────────
        DB::table('ubicacion')->insert([
            ['espacio' => 'Aula 101',         'capacidad' => 30],
            ['espacio' => 'Aula 202',         'capacidad' => 25],
            ['espacio' => 'Gimnasio',         'capacidad' => 50],
            ['espacio' => 'Sala de Cómputo 1','capacidad' => 20],
            ['espacio' => 'Virtual',          'capacidad' => 100],
            ['espacio' => 'Cancha Deportiva', 'capacidad' => 60],
        ]);

        // ── Actividades complementarias ────────────────────────────────
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
                'created_at'      => now(), 'updated_at' => now(),
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
                'created_at'      => now(), 'updated_at' => now(),
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
                'created_at'      => now(), 'updated_at' => now(),
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
                'created_at'      => now(), 'updated_at' => now(),
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
                'created_at'      => now(), 'updated_at' => now(),
            ],
        ]);

        // Actividad-Carrera: todas las carreras pueden acceder a todas las actividades
        $actividadIds = DB::table('actividad_complementaria')->pluck('id_actividad')->toArray();
        $carreraIds   = DB::table('carrera')->pluck('id_carrera')->toArray();
        foreach ($actividadIds as $idActividad) {
            foreach ($carreraIds as $idCarrera) {
                DB::table('actividad_carrera')->insert([
                    'id_actividad' => $idActividad,
                    'id_carrera'   => $idCarrera,
                ]);
            }
        }

        // ── Grupos de ejemplo ──────────────────────────────────────────
        $idSemestre1  = DB::table('semestre')->where('periodo', 1)->value('id_semestre');
        $idProgramacion = DB::table('actividad_complementaria')->where('nombre', 'Programación Web con Laravel')->value('id_actividad');
        $idIngles       = DB::table('actividad_complementaria')->where('nombre', 'Inglés Técnico')->value('id_actividad');
        $idFutbol       = DB::table('actividad_complementaria')->where('nombre', 'Fútbol Soccer')->value('id_actividad');

        $idGrupo1 = DB::table('grupo')->insertGetId([
            'id_actividad'          => $idProgramacion, 'id_semestre' => $idSemestre1, 'id_instructor' => null, 'id_ubicacion' => 4,
            'grupo' => 'A', 'cupo_maximo' => 20, 'cupo_ocupado' => 5, 'modalidad' => 'presencial',
            'materiales_requeridos' => 'Laptop', 'estatus' => 'abierta',
            'fecha_inicio' => '2026-02-01', 'fecha_fin' => '2026-05-31',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $idGrupo2 = DB::table('grupo')->insertGetId([
            'id_actividad'          => $idIngles, 'id_semestre' => $idSemestre1, 'id_instructor' => null, 'id_ubicacion' => 1,
            'grupo' => 'A', 'cupo_maximo' => 25, 'cupo_ocupado' => 8, 'modalidad' => 'presencial',
            'materiales_requeridos' => 'Ninguno', 'estatus' => 'abierta',
            'fecha_inicio' => '2026-02-01', 'fecha_fin' => '2026-05-31',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $idGrupo3 = DB::table('grupo')->insertGetId([
            'id_actividad'          => $idFutbol, 'id_semestre' => $idSemestre1, 'id_instructor' => null, 'id_ubicacion' => 3,
            'grupo' => 'A', 'cupo_maximo' => 30, 'cupo_ocupado' => 15, 'modalidad' => 'presencial',
            'materiales_requeridos' => 'Ropa deportiva', 'estatus' => 'abierta',
            'fecha_inicio' => '2026-02-01', 'fecha_fin' => '2026-05-31',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // ── Horarios de los grupos ─────────────────────────────────────
        DB::table('horario')->insert([
            ['id_grupo' => $idGrupo1, 'id_dia' => 1, 'hora_inicio' => '10:00:00', 'hora_fin' => '12:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['id_grupo' => $idGrupo1, 'id_dia' => 3, 'hora_inicio' => '10:00:00', 'hora_fin' => '12:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['id_grupo' => $idGrupo2, 'id_dia' => 2, 'hora_inicio' => '08:00:00', 'hora_fin' => '10:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['id_grupo' => $idGrupo2, 'id_dia' => 4, 'hora_inicio' => '08:00:00', 'hora_fin' => '10:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['id_grupo' => $idGrupo3, 'id_dia' => 5, 'hora_inicio' => '14:00:00', 'hora_fin' => '16:00:00', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
