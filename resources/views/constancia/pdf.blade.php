<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10.5px;
            color: #1a1a1a;
            background: #fff;
        }

        .page {
            width: 100%;
            padding: 28px 36px;
        }

        /* ── Encabezado usando tabla HTML pura (más confiable en dompdf) ── */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            border-bottom: 3px solid #4a0d8f;
            padding-bottom: 10px;
        }
        .header-table td { vertical-align: middle; padding-bottom: 10px; }
        .td-logo  { width: 70px; text-align: center; }
        .td-logo img { width: 60px; height: 60px; }
        .td-logo .logo-circle {
            width: 60px; height: 60px;
            border: 2px solid #4a0d8f;
            border-radius: 50%;
            display: inline-block;
            line-height: 60px;
            font-size: 9px;
            color: #4a0d8f;
            text-align: center;
        }
        .td-center { text-align: center; }
        .td-center .inst  { font-size: 8.5px; color: #555; letter-spacing: 0.5px; text-transform: uppercase; }
        .td-center .title { font-size: 17px; font-weight: bold; color: #4a0d8f; margin: 3px 0; }
        .td-center .sub   { font-size: 9.5px; color: #666; }
        .td-right { width: 105px; text-align: right; font-size: 8.5px; color: #777; line-height: 1.7; }

        /* ── Declaración ── */
        .declaracion {
            background: #f4effc;
            border-left: 4px solid #4a0d8f;
            padding: 11px 14px;
            margin-bottom: 14px;
            font-size: 11px;
            line-height: 1.75;
            text-align: justify;
        }
        .declaracion .hl { font-weight: bold; color: #4a0d8f; }

        /* ── Títulos de sección ── */
        .section-title {
            font-size: 9.5px;
            font-weight: bold;
            color: #fff;
            background: #4a0d8f;
            padding: 5px 10px;
            margin-bottom: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── Tablas de datos ── */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .info-table td {
            padding: 5px 8px;
            vertical-align: top;
            border-bottom: 1px solid #e8e0f5;
            font-size: 10px;
        }
        .info-table td.lbl {
            width: 22%;
            font-weight: bold;
            color: #4a0d8f;
        }
        .info-table td.val {
            width: 28%;
        }

        /* ── Calificación ── */
        .cal-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .cal-table td {
            text-align: center;
            padding: 10px 6px;
            border: 2px solid #e8e0f5;
        }
        .cal-label { font-size: 8.5px; color: #777; text-transform: uppercase; }
        .cal-value { font-size: 19px; font-weight: bold; color: #4a0d8f; margin-top: 4px; }
        .cal-value.green { color: #1a7a3c; }
        .cal-value.blue  { color: #1a5a8f; }
        .cal-sep { width: 12px; border: none; }

        /* ── Observaciones ── */
        .obs-box {
            background: #fafafa;
            border: 1px solid #e0e0e0;
            padding: 9px 13px;
            margin-bottom: 14px;
            font-size: 10.5px;
            color: #444;
            min-height: 34px;
        }

        /* ── Firmas ── */
        .firmas-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 32px;
        }
        .firmas-table td {
            text-align: center;
            padding: 0 10px;
            vertical-align: bottom;
        }
        .firma-space { height: 38px; }
        .firma-line {
            border-top: 1px solid #4a0d8f;
            margin: 0 auto 5px auto;
            width: 82%;
        }
        .firma-name { font-weight: bold; font-size: 9.5px; }
        .firma-role { font-size: 8.5px; color: #777; }

        /* ── Footer ── */
        .footer {
            border-top: 1px solid #d0c0f0;
            margin-top: 18px;
            padding-top: 7px;
            text-align: center;
            font-size: 8px;
            color: #999;
        }
    </style>
</head>
<body>
<div class="page">

    @php
        $alumno       = $inscripcion->alumno;
        $usuario      = $alumno->usuario;
        $grupo        = $inscripcion->grupo;
        $actividad    = $grupo->actividad;
        $instructor   = $grupo->instructor;
        $instUser     = $instructor?->usuario;
        $calificacion = $inscripcion->calificaciones->first();
        $desempenios  = ['excelente' => 'Excelente', 'bueno' => 'Bueno', 'malo' => 'Malo'];
        $desempenio   = $calificacion ? ($desempenios[$calificacion->desempenio] ?? $calificacion->desempenio) : 'N/A';
        $horarioTexto = $grupo->horarios->map(function($h) {
            return ucfirst($h->dia->nombre_dia ?? '') . ' ' .
                   substr($h->hora_inicio, 0, 5) . '–' . substr($h->hora_fin, 0, 5);
        })->implode(', ');
    @endphp

    {{-- ══ ENCABEZADO ══ --}}
    <table class="header-table">
        <tr>
            <td class="td-logo">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ public_path('images/logo.png') }}">
                @else
                    <div class="logo-circle">ITO</div>
                @endif
            </td>
            <td class="td-center">
                <div class="inst">Tecnológico Nacional de México</div>
                <div class="title">Instituto Tecnológico de Oaxaca</div>
                <div class="sub">Departamento de Actividades Complementarias</div>
            </td>
            <td class="td-right">
                Folio: <strong>#{{ str_pad($inscripcion->id_inscripcion, 5, '0', STR_PAD_LEFT) }}</strong><br>
                Expedida:<br>{{ \Carbon\Carbon::now()->isoFormat('D MMM YYYY') }}
            </td>
        </tr>
    </table>

    {{-- ══ DECLARACIÓN ══ --}}
    <div class="declaracion">
        El <strong>Instituto Tecnológico de Oaxaca</strong> hace constar que el/la alumno/a
        <span class="hl">{{ $usuario->nombre }} {{ $usuario->apellido_paterno }} {{ $usuario->apellido_materno }}</span>,
        con número de control <span class="hl">{{ $usuario->num_control }}</span>,
        perteneciente a la carrera de <span class="hl">{{ $alumno->carrera->nombre ?? 'N/A' }}</span>,
        cursando el <span class="hl">{{ $alumno->semestre_cursando }}° semestre</span>,
        <strong>aprobó satisfactoriamente</strong> la Actividad Complementaria
        "<span class="hl">{{ $actividad->nombre }}</span>",
        obteniendo <span class="hl">{{ $actividad->creditos }} crédito(s)</span>
        con desempeño <span class="hl">{{ $desempenio }}</span>.
    </div>

    {{-- ══ DATOS DEL ALUMNO ══ --}}
    <div class="section-title">Datos del Alumno</div>
    <table class="info-table">
        <tr>
            <td class="lbl">Nombre completo:</td>
            <td class="val" colspan="3">{{ $usuario->nombre }} {{ $usuario->apellido_paterno }} {{ $usuario->apellido_materno }}</td>
        </tr>
        <tr>
            <td class="lbl">N° de Control:</td>
            <td class="val">{{ $usuario->num_control }}</td>
            <td class="lbl">Semestre:</td>
            <td class="val">{{ $alumno->semestre_cursando }}°</td>
        </tr>
        <tr>
            <td class="lbl">Carrera:</td>
            <td class="val" colspan="3">{{ $alumno->carrera->nombre ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">Correo electrónico:</td>
            <td class="val">{{ $usuario->email }}</td>
            <td class="lbl">Créditos acumulados:</td>
            <td class="val">{{ $alumno->creditos_acumulados }}</td>
        </tr>
    </table>

    {{-- ══ DATOS DE LA ACTIVIDAD ══ --}}
    <div class="section-title">Datos de la Actividad</div>
    <table class="info-table">
        <tr>
            <td class="lbl">Actividad:</td>
            <td class="val" colspan="3">{{ $actividad->nombre }}</td>
        </tr>
        <tr>
            <td class="lbl">Descripción:</td>
            <td class="val" colspan="3">{{ $actividad->descripcion ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">Departamento:</td>
            <td class="val">{{ $actividad->departamento->nombre ?? 'N/A' }}</td>
            <td class="lbl">Créditos:</td>
            <td class="val">{{ $actividad->creditos }}</td>
        </tr>
        <tr>
            <td class="lbl">Nivel:</td>
            <td class="val">{{ ucfirst($actividad->nivel_actividad ?? 'N/A') }}</td>
            <td class="lbl">Modalidad:</td>
            <td class="val">{{ ucfirst($grupo->modalidad) }}</td>
        </tr>
        <tr>
            <td class="lbl">Grupo:</td>
            <td class="val">{{ $grupo->grupo }}</td>
            <td class="lbl">Lugar:</td>
            <td class="val">{{ $grupo->ubicacion->espacio ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">Periodo:</td>
            <td class="val">{{ $grupo->fecha_inicio }} — {{ $grupo->fecha_fin }}</td>
            <td class="lbl">Horario:</td>
            <td class="val">{{ $horarioTexto ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">Instructor:</td>
            <td class="val" colspan="3">
                @if($instUser)
                    {{ $instUser->nombre }} {{ $instUser->apellido_paterno }} {{ $instUser->apellido_materno }}
                    @if($instructor->especialidad) — <em>{{ $instructor->especialidad }}</em> @endif
                @else
                    N/A
                @endif
            </td>
        </tr>
        <tr>
            <td class="lbl">Fecha inscripción:</td>
            <td class="val" colspan="3">{{ \Carbon\Carbon::parse($inscripcion->fecha_inscripcion)->isoFormat('D [de] MMMM [de] YYYY') }}</td>
        </tr>
    </table>

    {{-- ══ CALIFICACIÓN ══ --}}
    <div class="section-title">Calificación Obtenida</div>
    <table class="cal-table">
        <tr>
            <td style="width:32%;">
                <div class="cal-label">Desempeño</div>
                <div class="cal-value">{{ $desempenio }}</div>
            </td>
            <td class="cal-sep"></td>
            <td style="width:32%;">
                <div class="cal-label">Créditos otorgados</div>
                <div class="cal-value green">{{ $actividad->creditos }}</div>
            </td>
            <td class="cal-sep"></td>
            <td style="width:32%;">
                <div class="cal-label">Estatus</div>
                <div class="cal-value blue" style="font-size:13px; margin-top:6px;">Aprobado ✓</div>
            </td>
        </tr>
    </table>

    @if($calificacion && $calificacion->observaciones)
        <div style="font-size:9.5px; font-weight:bold; color:#4a0d8f; margin-bottom:4px; margin-top:10px;">
            Observaciones del instructor:
        </div>
        <div class="obs-box">{{ $calificacion->observaciones }}</div>
    @endif

    {{-- ══ FIRMAS ══ --}}
    <table class="firmas-table">
        <tr>
            <td style="width:33%;">
                <div class="firma-space"></div>
                <div class="firma-line"></div>
                <div class="firma-name">
                    @if($instUser)
                        {{ $instUser->nombre }} {{ $instUser->apellido_paterno }}
                    @else
                        Instructor
                    @endif
                </div>
                <div class="firma-role">Instructor de la Actividad</div>
            </td>
            <td style="width:33%;">
                <div class="firma-space"></div>
                <div class="firma-line"></div>
                <div class="firma-name">{{ $usuario->nombre }} {{ $usuario->apellido_paterno }}</div>
                <div class="firma-role">Alumno — {{ $usuario->num_control }}</div>
            </td>
            <td style="width:33%;">
                <div class="firma-space"></div>
                <div class="firma-line"></div>
                <div class="firma-name">Jefe de Depto.</div>
                <div class="firma-role">Actividades Complementarias</div>
            </td>
        </tr>
    </table>

    {{-- ══ FOOTER ══ --}}
    <div class="footer">
        Instituto Tecnológico de Oaxaca &nbsp;·&nbsp;
        Víctor Bravo Ahuja S/N esq. Calzada Tecnológico, Oaxaca de Juárez, Oax. &nbsp;·&nbsp;
        Documento generado el {{ \Carbon\Carbon::now()->isoFormat('D [de] MMMM [de] YYYY, HH:mm') }} hrs.
    </div>

</div>
</body>
</html>
