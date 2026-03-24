<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Alumno;
use App\Models\Carrera;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected function redirectTo()
    {
        return match (auth()->user()->tipo_usuario) {
            'admin'      => route('admin.dashboard'),
            'instructor' => route('instructor.dashboard'),
            default      => route('home'),
        };
    }

    public function __construct()
    {
        $this->middleware('guest');
    }

    // Pasar las carreras a la vista de registro
    public function showRegistrationForm()
    {
        $carreras = Carrera::orderBy('nombre')->get();
        return view('auth.register', compact('carreras'));
    }

    protected function validator(array $data)
    {
        $rules = [
            'nombre'           => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'email'            => ['required', 'string', 'email', 'max:100', 'unique:USUARIO,email'],
            'num_control'      => ['nullable', 'integer'],
            'telefono'         => ['nullable', 'string', 'max:20'],
            'tipo_usuario'     => ['required', 'in:alumno,instructor,admin'],
            'password'         => ['required', 'string', 'min:6', 'confirmed'],
        ];

        // Carrera obligatoria solo para alumnos
        if (isset($data['tipo_usuario']) && $data['tipo_usuario'] === 'alumno') {
            $rules['id_carrera'] = ['required', 'exists:carrera,id_carrera'];
        }

        return Validator::make($data, $rules, [
            'nombre.required'           => 'El nombre es obligatorio.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'email.required'            => 'El correo es obligatorio.',
            'email.unique'              => 'Este correo ya está registrado.',
            'tipo_usuario.required'     => 'Selecciona un tipo de usuario.',
            'password.required'         => 'La contraseña es obligatoria.',
            'password.confirmed'        => 'Las contraseñas no coinciden.',
            'password.min'              => 'La contraseña debe tener al menos 6 caracteres.',
            'id_carrera.required'       => 'Debes seleccionar tu carrera.',
            'id_carrera.exists'         => 'La carrera seleccionada no es válida.',
        ]);
    }

    protected function create(array $data)
    {
        $user = User::create([
            'nombre'           => $data['nombre'],
            'apellido_paterno' => $data['apellido_paterno'],
            'apellido_materno' => $data['apellido_materno'] ?? null,
            'email'            => $data['email'],
            'contrasena'       => Hash::make($data['password']),
            'tipo_usuario'     => $data['tipo_usuario'],
            'num_control'      => $data['num_control'] ?? null,
            'telefono'         => $data['telefono'] ?? null,
            'ultimo_acceso'    => now(),
        ]);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $user->assignRole($data['tipo_usuario']);

        // Si es alumno, crear su registro en la tabla alumno
        if ($data['tipo_usuario'] === 'alumno') {
            Alumno::create([
                'id_alumno'           => $user->id,
                'id_carrera'          => $data['id_carrera'],
                'semestre_cursando'   => 1,
                'creditos_acumulados' => 0,
            ]);
        }

        return $user;
    }
}
