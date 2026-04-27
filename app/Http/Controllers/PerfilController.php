<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Alumno;
use App\Models\Carrera;

class PerfilController extends Controller
{
    // Actualización rápida desde el modal (nombre + email)
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:USUARIO,email,' . $user->id,
        ], [
            'name.required'  => 'El nombre es obligatorio.',
            'name.max'       => 'El nombre no puede superar 100 caracteres.',
            'email.required' => 'El correo es obligatorio.',
            'email.email'    => 'Ingresa un correo válido.',
            'email.unique'   => 'Este correo ya está en uso por otra cuenta.',
        ]);

        $partes   = explode(' ', trim($request->name), 2);
        $nombre   = $partes[0];
        $apellido = $partes[1] ?? $user->apellido_paterno;

        $user->update([
            'nombre'           => $nombre,
            'apellido_paterno' => $apellido,
            'email'            => $request->email,
        ]);

        return response()->json(['success' => true]);
    }

    // Vista del perfil completo del alumno
    public function show()
    {
        $user    = auth()->user();
        $alumno  = Alumno::with('carrera')->where('id_alumno', $user->id)->firstOrFail();
        $carreras = Carrera::orderBy('nombre')->get();

        return view('alumno.perfil', compact('user', 'alumno', 'carreras'));
    }

    // Actualización completa del perfil del alumno
    public function updateCompleto(Request $request)
    {
        $user   = auth()->user();
        $alumno = Alumno::where('id_alumno', $user->id)->firstOrFail();

        $request->validate([
            'nombre'            => 'required|string|max:100',
            'apellido_paterno'  => 'required|string|max:100',
            'apellido_materno'  => 'nullable|string|max:100',
            'email'             => 'required|email|max:100|unique:USUARIO,email,' . $user->id,
            'telefono'          => 'nullable|regex:/^[0-9]{10}$/',
            'semestre_cursando' => 'required|integer|min:1|max:12',
            'id_carrera'        => 'required|exists:carrera,id_carrera',
            'password'          => 'nullable|min:8|confirmed',
        ], [
            'nombre.required'           => 'El nombre es obligatorio.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'email.required'            => 'El correo es obligatorio.',
            'email.email'               => 'Ingresa un correo electrónico válido.',
            'email.unique'              => 'Este correo ya está en uso por otra cuenta.',
            'telefono.regex'            => 'El teléfono debe tener exactamente 10 dígitos numéricos.',
            'semestre_cursando.required'=> 'El semestre es obligatorio.',
            'semestre_cursando.min'     => 'El semestre mínimo es 1.',
            'semestre_cursando.max'     => 'El semestre máximo es 12.',
            'id_carrera.required'       => 'Debes seleccionar una carrera.',
            'id_carrera.exists'         => 'La carrera seleccionada no es válida.',
            'password.min'              => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'        => 'La confirmación de contraseña no coincide.',
        ]);

        $datosUsuario = [
            'nombre'           => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'email'            => $request->email,
            'telefono'         => $request->telefono,
        ];

        if ($request->filled('password')) {
            $datosUsuario['contrasena'] = Hash::make($request->password);
        }

        $user->update($datosUsuario);

        $alumno->update([
            'semestre_cursando' => $request->semestre_cursando,
            'id_carrera'        => $request->id_carrera,
        ]);

        return redirect()->route('alumno.perfil')
            ->with('success', 'Perfil actualizado correctamente.');
    }
}
