<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Alumno;
use App\Models\Instructor;
use App\Models\Carrera;
use App\Models\Departamento;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected function redirectTo()
    {
        return route('home');
    }

    public function __construct()
    {
        $this->middleware('guest');
    }

    // Pasar carreras y departamentos a la vista
    public function showRegistrationForm()
    {
        $carreras      = Carrera::orderBy('nombre')->get();
        $departamentos = Departamento::orderBy('nombre')->get();
        return view('auth.register', compact('carreras', 'departamentos'));
    }

    protected function validator(array $data)
    {
        $tipo = $data['tipo_registro'] ?? 'alumno';

        $rules = [
            'tipo_registro'    => ['required', Rule::in(['alumno', 'instructor'])],
            'nombre'           => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'email'            => ['required', 'string', 'email', 'max:100', 'unique:USUARIO,email'],
            'telefono'         => ['required', 'digits:10'],
            'password'         => ['required', 'string', 'min:6', 'confirmed'],
        ];

        $messages = [
            'nombre.required'           => 'El nombre es obligatorio.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'email.required'            => 'El correo electrónico es obligatorio.',
            'email.email'               => 'El correo electrónico no tiene un formato válido.',
            'email.unique'              => 'Este correo ya está registrado en el sistema.',
            'telefono.required'         => 'El teléfono es obligatorio.',
            'telefono.digits'           => 'El teléfono debe tener exactamente 10 dígitos.',
            'password.required'         => 'La contraseña es obligatoria.',
            'password.confirmed'        => 'Las contraseñas no coinciden.',
            'password.min'              => 'La contraseña debe tener al menos 6 caracteres.',
        ];

        if ($tipo === 'alumno') {
            $rules['id_carrera']      = ['required', 'exists:carrera,id_carrera'];
            $rules['semestre_actual'] = ['required', 'integer', 'min:1', 'max:12'];
            $rules['num_control']     = [
                'required',
                // 8 dígitos numéricos  O  C + 8 dígitos
                'regex:/^(\d{8}|C\d{8})$/',
                // Unicidad ignorando mayúsculas/minúsculas no aplica aquí
                // pero sí verificamos que no exista ya en la BD
                Rule::unique('USUARIO', 'num_control'),
            ];

            $messages['num_control.required'] = 'El número de control es obligatorio.';
            $messages['num_control.regex']     = 'El número de control debe ser de 8 dígitos numéricos (Ej: 20310001) o iniciar con "C" seguido de 8 dígitos (Ej: C20310001).';
            $messages['num_control.unique']    = 'Este número de control ya está registrado en el sistema.';
            $messages['id_carrera.required']   = 'Debes seleccionar tu carrera.';
            $messages['id_carrera.exists']     = 'La carrera seleccionada no es válida.';
            $messages['semestre_actual.required'] = 'Debes seleccionar el semestre que cursas.';
        }

        if ($tipo === 'instructor') {
            $rules['id_departamento'] = ['required', 'exists:departamento,id_departamento'];
            $rules['especialidad']    = ['required', 'string', 'max:150'];

            $messages['id_departamento.required'] = 'Debes seleccionar tu departamento.';
            $messages['id_departamento.exists']   = 'El departamento seleccionado no es válido.';
            $messages['especialidad.required']    = 'La especialidad es obligatoria.';
        }

        return Validator::make($data, $rules, $messages);
    }

    protected function create(array $data)
    {
        $tipo = $data['tipo_registro'];

        $user = User::create([
            'nombre'           => $data['nombre'],
            'apellido_paterno' => $data['apellido_paterno'],
            'apellido_materno' => $data['apellido_materno'] ?? null,
            'email'            => $data['email'],
            'contrasena'       => Hash::make($data['password']),
            'tipo_usuario'     => $tipo,
            'num_control'      => $tipo === 'alumno' ? ($data['num_control'] ?? null) : null,
            'telefono'         => $data['telefono'],
            'ultimo_acceso'    => now(),
        ]);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $user->assignRole($tipo);

        if ($tipo === 'alumno') {
            Alumno::create([
                'id_alumno'           => $user->id,
                'id_carrera'          => $data['id_carrera'],
                'semestre_cursando'   => $data['semestre_actual'],
                'creditos_acumulados' => 0,
            ]);
        }

        if ($tipo === 'instructor') {
            Instructor::create([
                'id_instructor'   => $user->id,
                'id_departamento' => $data['id_departamento'],
                'especialidad'    => $data['especialidad'],
            ]);
        }

        return $user;
    }
}
