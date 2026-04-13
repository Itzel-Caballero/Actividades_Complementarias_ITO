@extends('layouts.app')

@section('title', 'Log de Accesos')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Log de Accesos</h3>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-lg-12">

                {{-- Filtros --}}
                <div class="card mb-3">
                    <div class="card-body py-2">
                        <form action="{{ route('admin.reportes.accesos') }}" method="GET"
                              class="form-inline flex-wrap" style="gap:8px">
                            <input type="text" name="buscar" class="form-control form-control-sm"
                                   placeholder="Nombre o email..." value="{{ $buscar }}">
                            <select name="rol" class="form-control form-control-sm">
                                <option value="">Todos los roles</option>
                                <option value="admin"       {{ $rol == 'admin'       ? 'selected' : '' }}>Admin</option>
                                <option value="coordinador" {{ $rol == 'coordinador' ? 'selected' : '' }}>Coordinador</option>
                                <option value="instructor"  {{ $rol == 'instructor'  ? 'selected' : '' }}>Instructor</option>
                                <option value="alumno"      {{ $rol == 'alumno'      ? 'selected' : '' }}>Alumno</option>
                            </select>
                            <button class="btn btn-primary btn-sm" type="submit">
                                <i class="fa fa-filter"></i> Filtrar
                            </button>
                            <a href="{{ route('admin.reportes.accesos') }}" class="btn btn-secondary btn-sm">
                                <i class="fa fa-times"></i> Limpiar
                            </a>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>
                            <i class="fas fa-history mr-2 text-primary"></i>
                            Últimos Accesos por Usuario
                            <span class="badge badge-primary ml-2">{{ $usuarios->total() }}</span>
                        </h4>
                        <small class="text-muted"><i class="fas fa-lock mr-1"></i>Vista de solo lectura</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-sm">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre Completo</th>
                                        <th>Email</th>
                                        <th class="text-center">Rol</th>
                                        <th class="text-center">Tipo Usuario</th>
                                        <th class="text-center">Último Acceso</th>
                                        <th class="text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($usuarios as $u)
                                    @php
                                        $hace = $u->ultimo_acceso
                                            ? \Carbon\Carbon::parse($u->ultimo_acceso)->diffForHumans()
                                            : null;
                                        $reciente = $u->ultimo_acceso
                                            && \Carbon\Carbon::parse($u->ultimo_acceso)->gt(now()->subDays(7));
                                    @endphp
                                    <tr>
                                        <td>{{ ($usuarios->currentPage() - 1) * $usuarios->perPage() + $loop->iteration }}</td>
                                        <td>{{ $u->nombre_completo }}</td>
                                        <td>{{ $u->email }}</td>
                                        <td class="text-center">
                                            @foreach ($u->getRoleNames() as $r)
                                                <span class="badge badge-primary">{{ $r }}</span>
                                            @endforeach
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-light text-dark border">{{ $u->tipo_usuario }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if ($u->ultimo_acceso)
                                                <span title="{{ \Carbon\Carbon::parse($u->ultimo_acceso)->format('d/m/Y H:i:s') }}">
                                                    {{ $hace }}
                                                </span>
                                            @else
                                                <span class="text-muted">Nunca</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (!$u->ultimo_acceso)
                                                <span class="badge badge-secondary">Sin acceso</span>
                                            @elseif ($reciente)
                                                <span class="badge badge-success">Activo</span>
                                            @else
                                                <span class="badge badge-warning text-dark">Inactivo</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No hay usuarios con esos filtros.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end">
                            {!! $usuarios->appends(request()->query())->links() !!}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
