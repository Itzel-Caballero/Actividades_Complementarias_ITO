<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:USUARIO,email,' . $user->id,
        ]);

        // Separar nombre y apellido del campo "name"
        $partes = explode(' ', trim($request->name), 2);
        $nombre = $partes[0];
        $apellido = $partes[1] ?? $user->apellido_paterno;

        $user->update([
            'nombre'           => $nombre,
            'apellido_paterno' => $apellido,
            'email'            => $request->email,
        ]);

        return response()->json(['success' => true]);
    }
}
