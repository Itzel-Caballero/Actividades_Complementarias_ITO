<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Instructor;
use App\Models\Alumno;
use App\Models\Departamento;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $buscar       = trim($request->get('buscar', ''));
        $tipo_usuario = trim($request->get('tipo_usuario', ''));

        $usuarios = User::with('roles')
            ->where(function($query) use ($buscar) {
                if ($buscar) {
                    $query->where('nombre', 'LIKE', '%' . $buscar . '%')
                          ->orWhere('apellido_paterno', 'LIKE', '%' . $buscar . '%')
                          ->orWhere('email', 'LIKE', '%' . $buscar . '%')
                          ->orWhere('num_control', 'LIKE', '%' . $buscar . '%');
                }
            })
            ->when($tipo_usuario, fn($q) =>
                $q->where('tipo_usuario', $tipo_usuario)
            )
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('usuarios.index', compact('usuarios', 'buscar', 'tipo_usuario'));
    }

    public function create()
    {
        $roles        = Role::pluck('name', 'name')->all();
        $departamentos = Departamento::orderBy('nombre')->get();
        return view('usuarios.crear', compact('roles', 'departamentos'));
    }

    public function store(Request $request)
    {
        $rules = [
            'nombre'           => 'required',
            'apellido_paterno' => 'required',
            'email'            => 'required|email|unique:USUARIO,email',
            'password'         => 'required|same:confirm-password',
            'tipo_usuario'     => 'required|in:alumno,instructor,coordinador',
        ];

        // num_control solo obligatorio para alumnos
        if ($request->tipo_usuario === 'alumno') {
            $rules['num_control'] = 'required|numeric';
            $rules['id_carrera']  = 'required|exists:carrera,id_carrera';
        }

        // departamento obligatorio para instructores
        if ($request->tipo_usuario === 'instructor') {
            $rules['id_departamento'] = 'required|exists:departamento,id_departamento';
        }

        $this->validate($request, $rules);

    $user = User::create([
        'nombre'           => $request->nombre,
        'apellido_paterno' => $request->apellido_paterno,
        'apellido_materno' => $request->apellido_materno,
        'email'            => $request->email,
        'contrasena'       => Hash::make($request->password),
        'tipo_usuario'     => $request->tipo_usuario,
        'num_control'      => $request->num_control,
        'telefono'         => $request->telefono,
        'id_carrera'       => $request->id_carrera, // <-- Agregamos esta línea
    ]);

        // Insertar en tabla específica según tipo
        if ($request->tipo_usuario === 'instructor') {
            Instructor::create([
                'id_instructor'   => $user->id,
                'id_departamento' => $request->id_departamento,
                'especialidad'    => $request->especialidad,
            ]);
        }

        if ($request->tipo_usuario === 'alumno') {
            Alumno::create([
                'id_alumno'           => $user->id,
                'id_carrera'          => $request->id_carrera,
                'semestre_cursando'   => $request->semestre_cursando ?? 1,
                'creditos_acumulados' => 0,
            ]);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Determinar rol automáticamente basado en tipo_usuario
        $rol = match($request->tipo_usuario) {
            'alumno' => 'alumno',
            'instructor' => 'instructor',
            'coordinador' => 'coordinador',
            default => null
        };
        
        if ($rol) {
            try {
                $user->assignRole($rol);
            } catch (\Exception $e) {
                \Log::error('assignRole error: ' . $e->getMessage());
            }
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function show($id) {}

    public function edit($id)
    {
        $user          = User::findOrFail($id);
        $roles         = Role::pluck('name', 'name')->all();
        $userRole      = $user->roles->pluck('name', 'name')->all();
        $departamentos = Departamento::orderBy('nombre')->get();
        $instructor    = Instructor::where('id_instructor', $id)->first();
        return view('usuarios.editar', compact('user', 'roles', 'userRole', 'departamentos', 'instructor'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'nombre'           => 'required',
            'apellido_paterno' => 'required',
            'email'            => 'required|email|unique:USUARIO,email,' . $id,
            'password'         => 'nullable|same:confirm-password',
            'tipo_usuario'     => 'required|in:alumno,instructor,coordinador',
        ];

        // Validar departamento si es instructor
        if ($request->tipo_usuario === 'instructor') {
            $rules['id_departamento'] = 'required|exists:departamento,id_departamento';
        }

        $this->validate($request, $rules);

        $user = User::findOrFail($id);

        $data = [
            'nombre'           => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'email'            => $request->email,
            'tipo_usuario'     => $request->tipo_usuario,
            'num_control'      => $request->num_control,
            'telefono'         => $request->telefono,
        ];

        if (!empty($request->password)) {
            $data['contrasena'] = Hash::make($request->password);
        }

        $user->update($data);

        // Actualizar o crear registro en tabla instructor si corresponde
        if ($request->tipo_usuario === 'instructor') {
            Instructor::updateOrCreate(
                ['id_instructor' => $id],
                [
                    'id_departamento' => $request->id_departamento,
                    'especialidad'    => $request->especialidad,
                ]
            );
        }

        // Determinar rol automáticamente basado en tipo_usuario
        $rol = match($request->tipo_usuario) {
            'alumno'      => 'alumno',
            'instructor'  => 'instructor',
            'coordinador' => 'coordinador',
            default       => null
        };

        if ($rol) {
            DB::table('model_has_roles')->where('model_id', $id)->delete();
            $user->fresh()->assignRole($rol);
        }

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy($id)
{
    $usuario = User::find($id);

    // 1. Borramos la relación primero (el registro en la tabla alumnos)
    if ($usuario->alumno) {
        $usuario->alumno()->delete();
    }

    // 2. Ahora sí podemos borrar el usuario
    $usuario->delete();

    return redirect()->route('usuarios.index')
        ->with('success', 'El alumno ' . $usuario->nombre . ' ha sido dado de baja correctamente.');
}

    /**
     * Habilita o deshabilita un usuario (toggle activo).
     */
    public function toggle($id)
    {
        $user = User::findOrFail($id);
        $user->activo = !$user->activo;
        $user->save();

        $estado = $user->activo ? 'habilitado' : 'deshabilitado';
        return redirect()->route('usuarios.index')
                         ->with('success', "Usuario {$estado} correctamente.");
    }
}