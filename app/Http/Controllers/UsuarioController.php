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
        $usuarios = User::all();
        return view('usuarios.index', compact('usuarios'));
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
            'roles'            => 'required',
            'tipo_usuario'     => 'required',
        ];

        // num_control solo obligatorio para alumnos
        if ($request->tipo_usuario === 'alumno') {
            $rules['num_control']      = 'required|numeric';
            $rules['id_carrera']       = 'required|exists:carrera,id_carrera';
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
        try {
            $user->assignRole($request->input('roles'));
        } catch (\Exception $e) {
            \Log::error('assignRole error: ' . $e->getMessage());
        }

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario creado correctamente.');
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
        $this->validate($request, [
            'nombre'           => 'required',
            'apellido_paterno' => 'required',
            'email'            => 'required|email|unique:USUARIO,email,' . $id,
            'password'         => 'nullable|same:confirm-password',
            'roles'            => 'required',
        ]);

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
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->fresh()->assignRole($request->input('roles'));

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario eliminado correctamente.');
    }
}