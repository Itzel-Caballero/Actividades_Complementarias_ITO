<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CoordinadorDepartamento;
use App\Models\Departamento;
use App\Models\User;

class PanelCoordinadoresController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Lista todos los departamentos y su coordinador asignado (si tienen).
     */
    public function index()
    {
        $departamentos = Departamento::with(['coordinadorDepartamento.usuario'])
            ->orderBy('nombre')
            ->get();

        // Usuarios con rol coordinador disponibles para asignar
        $coordinadores = User::role('coordinador')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('coordinadores.index', compact('departamentos', 'coordinadores'));
    }

    /**
     * Asignar o reasignar coordinador a un departamento.
     * Regla: un departamento solo puede tener UN coordinador.
     */
    public function asignar(Request $request)
    {
        $request->validate([
            'id_departamento' => 'required|exists:departamento,id_departamento',
            'id_usuario'      => 'required|exists:USUARIO,id',
        ]);

        $idDepto   = $request->id_departamento;
        $idUsuario = $request->id_usuario;

        // Verificar que el usuario tiene rol coordinador
        $usuario = User::findOrFail($idUsuario);
        if (!$usuario->hasRole('coordinador')) {
            return back()->with('error', 'El usuario seleccionado no tiene el rol de Coordinador.');
        }

        // Si el usuario ya está coordinando OTRO departamento, bloqueamos
        $asignacionExistente = CoordinadorDepartamento::where('id_usuario', $idUsuario)
            ->where('id_departamento', '!=', $idDepto)
            ->first();

        if ($asignacionExistente) {
            $otroDep = $asignacionExistente->departamento->nombre ?? 'otro departamento';
            return back()->with('error', "Este coordinador ya está asignado al departamento «{$otroDep}». Un coordinador solo puede gestionar un departamento.");
        }

        // updateOrCreate: si el departamento ya tiene coordinador, lo reemplaza
        CoordinadorDepartamento::updateOrCreate(
            ['id_departamento' => $idDepto],
            ['id_usuario'      => $idUsuario]
        );

        $dep  = Departamento::find($idDepto);
        return back()->with('success', "Coordinador asignado correctamente al departamento «{$dep->nombre}».");
    }

    /**
     * Quitar el coordinador de un departamento.
     */
    public function quitar($id_departamento)
    {
        $registro = CoordinadorDepartamento::where('id_departamento', $id_departamento)->firstOrFail();
        $dep      = $registro->departamento->nombre ?? 'el departamento';
        $registro->delete();

        return back()->with('success', "Se removió al coordinador de «{$dep}».");
    }
}
