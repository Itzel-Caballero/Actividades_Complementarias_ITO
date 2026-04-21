@extends('layouts.app')

@section('content')
<section class="section">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h3 class="page__heading">
            <a href="{{ route('instructor.mis-grupos') }}" class="text-secondary mr-2">
                <i class="fas fa-arrow-left"></i>
            </a>
            {{ $calificacion ? 'Recalificar Alumno' : 'Calificar Alumno' }}
        </h3>
    </div>

    <div class="section-body">
        <div class="row justify-content-center">
            <div class="col-md-8">

                {{-- Info del alumno --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-user-graduate mr-2"></i>Información del Alumno
                        </h6>
                    </div>
                    <div class="card-body">
                        @php
                            $alumno  = $inscripcion->alumno;
                            $usuario = $alumno->usuario ?? null;
                        @endphp
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <strong>Nombre:</strong><br>
                                    {{ $usuario->nombre ?? '' }}
                                    {{ $usuario->apellido_paterno ?? '' }}
                                    {{ $usuario->apellido_materno ?? '' }}
                                </p>
                                <p class="mb-1">
                                    <strong>No. Control:</strong>
                                    <span class="badge badge-light border">{{ $usuario->num_control ?? 'N/A' }}</span>
                                </p>
                                <p class="mb-0">
                                    <strong>Teléfono:</strong>
                                    {{ $usuario->telefono ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <strong>Carrera:</strong><br>
                                    {{ $alumno->carrera->nombre ?? 'N/A' }}
                                </p>
                                <p class="mb-1">
                                    <strong>Actividad:</strong><br>
                                    {{ $inscripcion->grupo->actividad->nombre ?? 'N/A' }}
                                    <span class="badge badge-secondary ml-1">Grupo {{ $inscripcion->grupo->grupo ?? '' }}</span>
                                </p>
                                <p class="mb-0">
                                    <strong>Estatus:</strong>
                                    @switch($inscripcion->estatus)
                                        @case('inscrito')  <span class="badge badge-secondary ml-1">Inscrito</span>  @break
                                        @case('cursando')  <span class="badge badge-info ml-1">Cursando</span>       @break
                                        @case('aprobado')  <span class="badge badge-success ml-1">Aprobado</span>    @break
                                        @case('reprobado') <span class="badge badge-danger ml-1">Reprobado</span>    @break
                                        @default           <span class="badge badge-warning ml-1">{{ ucfirst($inscripcion->estatus) }}</span>
                                    @endswitch
                                </p>
                            </div>
                        </div>

                        {{-- Calificación previa si existe --}}
                        @if ($calificacion)
                        <hr class="my-2">
                        <div class="alert alert-warning py-2 mb-0">
                            <i class="fas fa-info-circle mr-2"></i>
                            Este alumno ya tiene una calificación registrada:
                            <strong>
                                @switch($calificacion->desempenio)
                                    @case('excelente') Excelente @break
                                    @case('bueno')     Bueno     @break
                                    @case('malo')      Malo      @break
                                @endswitch
                            </strong>
                            @if($calificacion->observaciones)
                                — "{{ $calificacion->observaciones }}"
                            @endif
                            . Puedes modificarla a continuación.
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Formulario --}}
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-star mr-2 text-warning"></i>
                            {{ $calificacion ? 'Modificar Calificación' : 'Registrar Calificación' }}
                        </h6>
                    </div>
                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('instructor.guardarCalificacion', $inscripcion->id_inscripcion) }}"
                              method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Tres opciones de desempeño --}}
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    Desempeño del alumno <span class="text-danger">*</span>
                                </label>
                                <div class="row mt-2">
                                    {{-- Excelente --}}
                                    <div class="col-md-4">
                                        <div class="card border-light opcion-card" id="card-excelente"
                                             onclick="seleccionar('excelente')" style="cursor:pointer;transition:all .2s;">
                                            <div class="card-body text-center py-3">
                                                <i class="fas fa-award fa-2x text-success mb-2"></i>
                                                <h6 class="mb-1 text-success">Excelente</h6>
                                                <small class="text-muted">Superó las expectativas</small>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Bueno --}}
                                    <div class="col-md-4">
                                        <div class="card border-light opcion-card" id="card-bueno"
                                             onclick="seleccionar('bueno')" style="cursor:pointer;transition:all .2s;">
                                            <div class="card-body text-center py-3">
                                                <i class="fas fa-thumbs-up fa-2x text-primary mb-2"></i>
                                                <h6 class="mb-1 text-primary">Bueno</h6>
                                                <small class="text-muted">Cumplió satisfactoriamente</small>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Malo --}}
                                    <div class="col-md-4">
                                        <div class="card border-light opcion-card" id="card-malo"
                                             onclick="seleccionar('malo')" style="cursor:pointer;transition:all .2s;">
                                            <div class="card-body text-center py-3">
                                                <i class="fas fa-thumbs-down fa-2x text-danger mb-2"></i>
                                                <h6 class="mb-1 text-danger">Malo</h6>
                                                <small class="text-muted">No cumplió los requisitos</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="desempenio" id="desempenio"
                                       value="{{ old('desempenio', $calificacion->desempenio ?? '') }}">
                                @error('desempenio')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Observaciones --}}
                            <div class="form-group mt-3">
                                <label class="font-weight-bold">Observaciones</label>
                                <textarea name="observaciones"
                                          class="form-control @error('observaciones') is-invalid @enderror"
                                          rows="3" maxlength="255"
                                          placeholder="Notas adicionales (opcional)...">{{ old('observaciones', $calificacion->observaciones ?? '') }}</textarea>
                                @error('observaciones')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('instructor.mis-grupos') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-{{ $calificacion ? 'warning' : 'primary' }}">
                                    <i class="fas fa-save mr-1"></i>
                                    {{ $calificacion ? 'Actualizar calificación' : 'Guardar calificación' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    const colores = {
        excelente: 'border-success shadow',
        bueno:     'border-primary shadow',
        malo:      'border-danger shadow',
    };

    document.addEventListener('DOMContentLoaded', function () {
        const val = document.getElementById('desempenio').value;
        if (val) resaltar(val);
    });

    function seleccionar(valor) {
        document.getElementById('desempenio').value = valor;
        resaltar(valor);
    }

    function resaltar(valor) {
        ['excelente', 'bueno', 'malo'].forEach(function (opcion) {
            const card = document.getElementById('card-' + opcion);
            card.className = 'card ' + (opcion === valor ? colores[opcion] : 'border-light');
        });
    }
</script>
@endsection
