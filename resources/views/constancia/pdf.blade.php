<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            background: #fff;
        }

        .page {
            width: 100%;
            padding: 30px 40px;
        }

        /* ── Header ── */
        .header {
            display: table;
            width: 100%;
            border-bottom: 3px solid #4a0d8f;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header-logo {
            display: table-cell;
            width: 80px;
            vertical-align: middle;
            text-align: center;
        }
        .header-logo img {
            width: 65px;
            height: 65px;
        }
        .header-text {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }
        .header-text .inst {
            font-size: 9px;
            color: #555;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header-text .title {
            font-size: 18px;
            font-weight: bold;
            color: #4a0d8f;
            margin: 3px 0;
        }
        .header-text .subtitle {
            font-size: 10px;
            color: #666;
        }
        .header-right {
            display: table-cell;
            width: 100px;
            vertical-align: middle;
            text-align: right;
            font-size: 9px;
            color: #777;
        }

        /* ── Cuerpo declaratorio ── */
        .declaracion {
            background: #f4effc;
            border-left: 4px solid #4a0d8f;
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 16px;
            font-size: 11.5px;
            line-height: 1.7;
            text-align: justify;
        }
        .declaracion .highlight {
            font-weight: bold;
            color: #4a0d8f;
        }

        /* ── Secciones de datos ── */
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #fff;
            background: #4a0d8f;
            padding: 5px 10px;
            border-radius: 3px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .info-table td {
            padding: 5px 8px;
            vertical-align: top;
            border-bottom: 1px solid #e8e0f5;
        }
        .info-table td.label {
            width: 32%;
            font-weight: bold;
            color: #4a0d8f;
            white-space: nowrap;
        }
        .info-table td.value {
            color: #1a1a1a;
        }

        /* ── Calificación ── */
        .calificacion-box {
            display: table;
            width: 100%;
            margin-bottom: 14px;
        }
        .cal-item {
            display: table-cell;
            text-align: center;
            padding: 10px;
            border: 2px solid #e8e0f5;
            border-radius: 6px;
        }
        .cal-item .cal-label {
            font-size: 9px;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .cal-item .cal-value {
            font-size: 20px;
            font-weight: bold;
            color: #4a0d8f;
            margin-top: 4px;
        }
        .cal-item.green .cal-value { color: #1a7a3c; }
        .cal-item.blue  .cal-value { color: #1a5a8f; }

        /* ── Observaciones ── */
        .observaciones-box {
            background: #fafafa;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 16px;
            font-size: 11px;
            color: #444;
            min-height: 36px;
        }

        /* ── Firmas ── */
        .firmas {
            display: table;
            width: 100%;
            margin-top: 30px;
        }
        .firma-cell {
            display: table-cell;
            width: 33%;
            text-align: center;
            padding: 0 10px;
        }
        .firma-line {
            border-top: 1px solid #4a0d8f;
            margin: 0 auto 6px auto;
            width: 85%;
        }
        .firma-name {
            font-weight: bold;
            font-size: 10px;
            color: #1a1a1a;
        }
        .firma-role {
            font-size: 9px;
            color: #777;
        }

        /* ── Footer ── */
        .footer {
            border-top: 1px solid #d0c0f0;
            margin-top: 20px;
            padding-top: 8px;
            text-align: center;
            font-size: 8.5px;
            color: #999;
        }

        /* ── Folio badge ── */
        .folio {
            float: right;
            background: #f0ebfb;
            border: 1px solid #c0a8e8;
            border-radius: 4px;
            padding: 3px 10px;
            font-size: 9px;
            color: #4a0d8f;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- ══ ENCABEZADO ══ --}}
    <div class="header">
        <div class="header-logo">
            {{-- Logo del ITO (si existe en public/images) --}}
            @if(file_exists(public_path('images/logo.png')))
                <img src="{{ public_path('images/logo.png') }}">
            @else
                <div style="width:65px;height:65px;border:2px solid #4a0d8f;border-radius:50%;display:inline-block;line-height:65px;font-size:9px;color:#4a0d8f;text-align:center;">ITO</div>
            @endif
        </div>
        <div class="header-text">
            <div class="inst">Tecnológico Nacional de México</div>
            <div class="title">Instituto Tecnológico de Oaxaca</div>
            <div class="subtitle">Departamento de Actividades Complementarias</div>
        </div>
        <div class="header-right">
            Folio: <strong>#{{ str_pad($inscripcion->id_inscripcion, 5, '0', STR_PAD_LEFT) }}</strong><br>
            Expedida: {{ \Carbon\Carbon::now()->isoFormat('D [de] MMMM [de] YYYY') }}
        </div>
    </div>

    {{-- ══ DECLARACIÓN ══ --}}
    @php
        $alumno      = $inscripcion->alumno;
        $usuario     = $alumno->usuario;
        $grupo       = $inscripcion->grupo;
        $actividad   = $grupo->actividad;
        $instructor  = $grupo->instructor;
        $instUser    = $instructor?->usuario;
        $calificacion = $inscripcion->calificaciones->first();
        $desempenios  = ['excelente' => 'Excelente', 'bueno' => 'Bueno', 'malo' => 'Malo'];
        $desempenio   = $calificacion ? ($desempenios[$calificacion->desempenio] ?? $calificacion->desempenio) : 'N/A';
        $horarioTexto = $grupo->horarios->map(function($h) {
            return ucfirst($h->dia->nombre_dia ?? '') . ' ' . substr($h->hora_inicio,0,5) . '–' . substr($h->hora_fin,0,5);
        })->implode(' | ');
    @endphp

    <div class="declaracion">
        El <strong>Instituto Tecnológico de Oaxaca</strong> hace constar que el/la alumno/a
        <span class="highlight">{{ $usuario->nombre }} {{ $usuario->apellido_paterno }} {{ $usuario->apellido_materno }}</span>,
        con número de control <span class="highlight">{{ $usuario->num_control }}</span>,
        perteneciente a la carrera de <span class="highlight">{{ $alumno->carrera->nombre ?? 'N/A' }}</span>,
        cursando el <span class="highlight">{{ $alumno->semestre_cursando }}° semestre</span>,
        <strong>aprobó satisfactoriamente</strong> la Actividad Complementaria
        "<span class="highlight">{{ $actividad->nombre }}</span>",
        obteniendo <span class="highlight">{{ $actividad->creditos }} crédito(s)</span>
        con desempeño <span class="highlight">{{ $desempenio }}</span>.
    </div>

    {{-- ══ DATOS DEL ALUMNO ══ --}}
    <div class="section-title">Datos del Alumno</div>
    <table class="info-table">
        <tr>
            <td class="label">Nombre completo:</td>
            <td class="value">{{ $usuario->nombre }} {{ $usuario->apellido_paterno }} {{ $usuario->apellido_materno }}</td>
            <td class="label">N° de Control:</td>
            <td class="value">{{ $usuario->num_control }}</td>
        </tr>
        <tr>
            <td class="label">Carrera:</td>
            <td class="value">{{ $alumno->carrera->nombre ?? 'N/A' }}</td>
            <td class="label">Semestre:</td>
            <td class="value">{{ $alumno->semestre_cursando }}°</td>
        </tr>
        <tr>
            <td class="label">Correo electrónico:</td>
            <td class="value">{{ $usuario->email }}</td>
            <td class="label">Créditos acumulados:</td>
            <td class="value">{{ $alumno->creditos_acumulados }}</td>
        </tr>
    </table>

    {{-- ══ DATOS DE LA ACTIVIDAD ══ --}}
    <div class="section-title">Datos de la Actividad</div>
    <table class="info-table">
        <tr>
            <td class="label">Actividad:</td>
            <td class="value" colspan="3">{{ $actividad->nombre }}</td>
        </tr>
        <tr>
            <td class="label">Descripción:</td>
            <td class="value" colspan="3">{{ $actividad->descripcion ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Departamento:</td>
            <td class="value">{{ $actividad->departamento->nombre ?? 'N/A' }}</td>
            <td class="label">Créditos:</td>
            <td class="value">{{ $actividad->creditos }}</td>
        </tr>
        <tr>
            <td class="label">Nivel:</td>
            <td class="value">{{ ucfirst($actividad->nivel_actividad ?? 'N/A') }}</td>
            <td class="label">Modalidad:</td>
            <td class="value">{{ ucfirst($grupo->modalidad) }}</td>
        </tr>
        <tr>
            <td class="label">Grupo:</td>
            <td class="value">{{ $grupo->grupo }}</td>
            <td class="label">Lugar:</td>
            <td class="value">{{ $grupo->ubicacion->espacio ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Periodo:</td>
            <td class="value">{{ $grupo->fecha_inicio }} — {{ $grupo->fecha_fin }}</td>
            <td class="label">Horario:</td>
            <td class="value">{{ $horarioTexto ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Instructor:</td>
            <td class="value">
                @if($instUser)
                    {{ $instUser->nombre }} {{ $instUser->apellido_paterno }} {{ $instUser->apellido_materno }}
                    @if($instructor->especialidad)
                        — <em>{{ $instructor->especialidad }}</em>
                    @endif
                @else
                    N/A
                @endif
            </td>
            <td class="label">Fecha inscripción:</td>
            <td class="value">{{ \Carbon\Carbon::parse($inscripcion->fecha_inscripcion)->isoFormat('D MMM YYYY') }}</td>
        </tr>
    </table>

    {{-- ══ CALIFICACIÓN ══ --}}
    <div class="section-title">Calificación Obtenida</div>
    <table style="width:100%;margin-bottom:14px;">
        <tr>
            <td style="width:33%;text-align:center;padding:10px;border:2px solid #e8e0f5;border-radius:6px;">
                <div style="font-size:9px;color:#777;text-transform:uppercase;">Desempeño</div>
                <div style="font-size:20px;font-weight:bold;color:#4a0d8f;margin-top:4px;">{{ $desempenio }}</div>
            </td>
            <td style="width:5%;"></td>
            <td style="width:28%;text-align:center;padding:10px;border:2px solid #e8e0f5;border-radius:6px;">
                <div style="font-size:9px;color:#777;text-transform:uppercase;">Créditos otorgados</div>
                <div style="font-size:20px;font-weight:bold;color:#1a7a3c;margin-top:4px;">{{ $actividad->creditos }}</div>
            </td>
            <td style="width:5%;"></td>
            <td style="width:29%;text-align:center;padding:10px;border:2px solid #e8e0f5;border-radius:6px;">
                <div style="font-size:9px;color:#777;text-transform:uppercase;">Estatus</div>
                <div style="font-size:14px;font-weight:bold;color:#1a5a8f;margin-top:4px;">Aprobado ✓</div>
            </td>
        </tr>
    </table>

    @if($calificacion && $calificacion->observaciones)
        <div style="font-size:10px;font-weight:bold;color:#4a0d8f;margin-bottom:4px;">Observaciones del instructor:</div>
        <div class="observaciones-box">{{ $calificacion->observaciones }}</div>
    @endif

    {{-- ══ FIRMAS ══ --}}
    <table class="firmas" style="margin-top:35px;">
        <tr>
            <td class="firma-cell">
                <div style="height:40px;"></div>
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
            <td class="firma-cell">
                <div style="height:40px;"></div>
                <div class="firma-line"></div>
                <div class="firma-name">{{ $usuario->nombre }} {{ $usuario->apellido_paterno }}</div>
                <div class="firma-role">Alumno — {{ $usuario->num_control }}</div>
            </td>
            <td class="firma-cell">
                <div style="height:40px;"></div>
                <div class="firma-line"></div>
                <div class="firma-name">Jefe de Depto.</div>
                <div class="firma-role">Actividades Complementarias</div>
            </td>
        </tr>
    </table>

    {{-- ══ FOOTER ══ --}}
    <div class="footer">
        Instituto Tecnológico de Oaxaca &nbsp;·&nbsp; Víctor Bravo Ahuja S/N esq. Calzada Tecnológico, Oaxaca de Juárez, Oax. &nbsp;·&nbsp;
        Documento generado el {{ \Carbon\Carbon::now()->isoFormat('D [de] MMMM [de] YYYY, HH:mm') }} hrs.
    </div>

</div>
</body>
</html>
