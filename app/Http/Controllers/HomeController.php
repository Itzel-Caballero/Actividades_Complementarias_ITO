<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Inscripcion;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Dashboard personalizado para alumno
        if (auth()->user()->hasRole('alumno')) {
            $alumno = Alumno::with('carrera')
                ->where('id_alumno', auth()->id())
                ->first();

            $inscripcionActiva = null;
            if ($alumno) {
                $inscripcionActiva = Inscripcion::with([
                    'grupo.actividad',
                    'grupo.horarios.dia',
                    'grupo.ubicacion',
                ])->where('id_alumno', $alumno->id_alumno)
                  ->whereIn('estatus', ['inscrito', 'cursando'])
                  ->first();
            }

            return view('alumno.dashboard', compact('alumno', 'inscripcionActiva'));
        }

        // Dashboard por defecto (admin / instructor)
        return view('home');
    }
}
