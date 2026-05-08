<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #000;
            background: #fff;
        }

        .page { padding: 20px 28px; }

        /* ── Encabezado simple ── */
        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .header-logo {
            display: table-cell;
            width: 60px;
            vertical-align: middle;
        }
        .header-logo img { width: 50px; height: 50px; }
        .header-logo .logo-text {
            font-size: 11px;
            font-weight: bold;
            border: 2px solid #000;
            width: 48px;
            height: 48px;
            line-height: 48px;
            text-align: center;
            display: inline-block;
        }
        .header-center {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }
        .header-center .inst  { font-size: 8px; }
        .header-center .title { font-size: 13px; font-weight: bold; margin: 2px 0; }
        .header-center .sub   { font-size: 8.5px; }
        .header-right {
            display: table-cell;
            width: 100px;
            vertical-align: middle;
            text-align: right;
            font-size: 8.5px;
            line-height: 1.7;
        }

        /* ── Título del documento ── */
        .doc-title {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #000;
            padding: 4px 0;
            margin-bottom: 8px;
        }

        /* ── Datos del grupo en tabla simple ── */
        .datos-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 9px;
        }
        .datos-table td {
            padding: 3px 6px;
            border: 1px solid #999;
        }
        .datos-table td.lbl {
            font-weight: bold;
            width: 80px;
            background: #f0f0f0;
        }

        /* ── Instrucciones ── */
        .instrucciones {
            font-size: 8px;
            margin-bottom: 8px;
            color: #333;
        }

        /* ── Tabla de asistencia ── */
        .att-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
        }
        .att-table th {
            border: 1px solid #555;
            padding: 4px 2px;
            text-align: center;
            background: #e8e8e8;
            font-size: 7.5px;
        }
        .att-table th.th-left { text-align: left; padding-left: 4px; }
        .att-table td {
            border: 1px solid #888;
            padding: 3px 2px;
            text-align: center;
            vertical-align: middle;
        }
        .att-table td.td-left { text-align: left; padding-left: 4px; }
        .att-table tbody tr:nth-child(even) { background: #f7f7f7; }

        .sesion-th { width: 22px; font-size: 7px; }
        .sesion-fecha { font-size: 6.5px; font-weight: normal; }

        /* Cuadro para marcar asistencia */
        .check-box {
            width: 14px; height: 14px;
            border: 1px solid #555;
            margin: 0 auto;
        }

        /* ── Firmas ── */
        .firmas {
            display: table;
            width: 100%;
            margin-top: 28px;
        }
        .firma-cell {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 0 30px;
        }
        .firma-line {
            border-top: 1px solid #000;
            margin: 0 auto 4px auto;
            width: 80%;
        }
        .firma-nombre { font-size: 9px; font-weight: bold; }
        .firma-cargo  { font-size: 8px; color: #444; }

        /* ── Pie de página ── */
        .footer {
            border-top: 1px solid #bbb;
            margin-top: 12px;
            padding-top: 4px;
            text-align: center;
            font-size: 7.5px;
            color: #666;
        }
    </style>
</head>
<body>
<div class="page">

    @php
        $actividad    = $grupo->actividad;
        $semestre     = ($grupo->semestre->año ?? '') . '-' . ($grupo->semestre->periodo ?? '');
        $ubicacion    = $grupo->ubicacion->espacio ?? 'Virtual';

        $horarioTexto = $grupo->horarios->map(function ($h) {
            return ucfirst($h->dia->nombre_dia ?? '') . ' ' .
                   \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') . '–' .
                   \Carbon\Carbon::parse($h->hora_fin)->format('H:i');
        })->implode(', ');

        $fechaInicio = \Carbon\Carbon::parse($grupo->fecha_inicio);
        $fechaFin    = \Carbon\Carbon::parse($grupo->fecha_fin);

        // Calcular fechas reales de sesión según días de horario
        $diasHorario = $grupo->horarios
            ->map(fn($h) => strtolower($h->dia->nombre_dia ?? ''))
            ->filter()->unique()->values();

        $sesiones = collect();
        if ($diasHorario->isNotEmpty()) {
            $cursor = $fechaInicio->copy();
            while ($cursor->lte($fechaFin)) {
                $hoyNorm = str_replace(['á','é','í','ó','ú'], ['a','e','i','o','u'],
                                       strtolower($cursor->isoFormat('dddd')));
                foreach ($diasHorario as $dia) {
                    $diaNorm = str_replace(['á','é','í','ó','ú'], ['a','e','i','o','u'], $dia);
                    if ($diaNorm === $hoyNorm) { $sesiones->push($cursor->copy()); break; }
                }
                $cursor->addDay();
            }
        }

        $numSesiones = max($sesiones->count(), 8);

        $inscripciones = $grupo->inscripciones->sortBy(function ($i) {
            $u = $i->alumno->usuario;
            return ($u->apellido_paterno ?? '') . ($u->apellido_materno ?? '') . ($u->nombre ?? '');
        })->values();
    @endphp

    {{-- ENCABEZADO --}}
    <div class="header">
        <div class="header-logo">
            @if(file_exists(public_path('images/logo.png')))
                <img src="{{ public_path('images/logo.png') }}">
            @else
                <div class="logo-text">ITO</div>
            @endif
        </div>
        <div class="header-center">
            <div class="inst">Tecnológico Nacional de México</div>
            <div class="title">Instituto Tecnológico de Oaxaca</div>
            <div class="sub">Departamento de Actividades Complementarias</div>
        </div>
        <div class="header-right">
            Fecha: {{ now()->format('d/m/Y') }}<br>
            Alumnos: {{ $inscripciones->count() }}
        </div>
    </div>

    {{-- TÍTULO --}}
    <div class="doc-title">Lista de Asistencia</div>

    {{-- DATOS DEL GRUPO --}}
    <table class="datos-table">
        <tr>
            <td class="lbl">Actividad:</td>
            <td>{{ $actividad->nombre ?? '—' }}</td>
            <td class="lbl">Grupo:</td>
            <td>{{ $grupo->grupo }}</td>
            <td class="lbl">Semestre:</td>
            <td>{{ $semestre }}</td>
        </tr>
        <tr>
            <td class="lbl">Instructor:</td>
            <td colspan="3">
                {{ $instructorUser->nombre }}
                {{ $instructorUser->apellido_paterno }}
                {{ $instructorUser->apellido_materno ?? '' }}
            </td>
            <td class="lbl">Créditos:</td>
            <td>{{ $actividad->creditos ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">Periodo:</td>
            <td>{{ $fechaInicio->format('d/m/Y') }} – {{ $fechaFin->format('d/m/Y') }}</td>
            <td class="lbl">Lugar:</td>
            <td>{{ $ubicacion }}</td>
            <td class="lbl">Horario:</td>
            <td>{{ $horarioTexto ?: '—' }}</td>
        </tr>
    </table>

    {{-- INSTRUCCIONES --}}
    <div class="instrucciones">
        <strong>Instrucciones:</strong>
        Marque en cada cuadro según corresponda:
        <strong>P</strong> = Presente &nbsp;
        <strong>A</strong> = Ausente &nbsp;
        <strong>R</strong> = Retardo &nbsp;
        <strong>J</strong> = Justificado.
        Al final del periodo anote el total de asistencias y recabe la firma del alumno.
    </div>

    {{-- TABLA DE ASISTENCIAS --}}
    <table class="att-table">
        <thead>
            <tr>
                <th style="width:16px;">#</th>
                <th class="th-left" style="width:58px;">No. Control</th>
                <th class="th-left" style="min-width:110px;">Nombre completo</th>
                <th style="width:70px; font-size:7px;">Carrera</th>
                <th style="width:20px;">Sem.</th>
                {{-- Columnas de sesiones --}}
                @if($sesiones->isNotEmpty())
                    @foreach($sesiones as $i => $fecha)
                        <th class="sesion-th">
                            S{{ $i + 1 }}<br>
                            <span class="sesion-fecha">{{ $fecha->format('d/m') }}</span>
                        </th>
                    @endforeach
                @else
                    @for($s = 1; $s <= $numSesiones; $s++)
                        <th class="sesion-th">S{{ $s }}</th>
                    @endfor
                @endif
                <th style="width:28px; font-size:7px;">Total</th>
                <th style="width:45px; font-size:7px;">Firma</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inscripciones as $idx => $inscripcion)
                @php
                    $alumno  = $inscripcion->alumno;
                    $usuario = $alumno->usuario;
                @endphp
                <tr>
                    <td style="color:#666;">{{ $idx + 1 }}</td>
                    <td class="td-left">{{ $usuario->num_control ?? 'N/A' }}</td>
                    <td class="td-left">
                        {{ $usuario->apellido_paterno ?? '' }}
                        {{ $usuario->apellido_materno ?? '' }},
                        {{ $usuario->nombre ?? '—' }}
                    </td>
                    <td style="font-size:7px;">{{ $alumno->carrera->nombre ?? 'N/A' }}</td>
                    <td>{{ $alumno->semestre_cursando ?? '—' }}</td>
                    @for($s = 0; $s < $numSesiones; $s++)
                        <td style="height:18px; padding:2px;">
                            <div class="check-box"></div>
                        </td>
                    @endfor
                    <td style="height:18px;"></td>
                    <td style="height:18px;"></td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 5 + $numSesiones + 2 }}"
                        style="text-align:center; padding:10px; color:#666;">
                        No hay alumnos inscritos en este grupo.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FIRMAS --}}
    <table class="firmas">
        <tr>
            <td class="firma-cell">
                <div style="height:32px;"></div>
                <div class="firma-line"></div>
                <div class="firma-nombre">
                    {{ $instructorUser->nombre }}
                    {{ $instructorUser->apellido_paterno }}
                    {{ $instructorUser->apellido_materno ?? '' }}
                </div>
                <div class="firma-cargo">Instructor de la Actividad</div>
            </td>
            <td class="firma-cell">
                <div style="height:32px;"></div>
                <div class="firma-line"></div>
                <div class="firma-nombre">Jefe de Depto. Actividades Complementarias</div>
                <div class="firma-cargo">Vo. Bo.</div>
            </td>
        </tr>
    </table>

    {{-- PIE --}}
    <div class="footer">
        Instituto Tecnológico de Oaxaca &nbsp;&middot;&nbsp;
        Víctor Bravo Ahuja S/N esq. Calzada Tecnológico, Oaxaca de Juárez, Oax. &nbsp;&middot;&nbsp;
        Generado el {{ now()->format('d/m/Y H:i') }}
    </div>

</div>
</body>
</html>
