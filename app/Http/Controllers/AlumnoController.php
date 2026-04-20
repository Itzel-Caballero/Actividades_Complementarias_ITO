<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno; // Asegúrate de que el modelo se llame así
use App\Models\Carrera;

class AlumnoController extends Controller
{
    // Muestra el formulario para inscribir (crear) un nuevo alumno
    public function create()
    {
        $carreras = Carrera::all();
        return view('admin.alumnos.create', compact('carreras'));
    }

    // Guarda el nuevo alumno en la base de datos
    public function store(Request $request)
    {
        // Aquí agregarías la validación y el guardado
        // Por ahora, un ejemplo rápido:
        // Alumno::create($request->all());
        return redirect()->route('admin.reportes.alumnos')->with('success', 'Alumno inscrito con éxito.');
    }

    // Elimina (Da de baja) al alumno
    public function destroy($id)
{
    try {
        // Buscamos al alumno
        $alumno = Alumno::findOrFail($id);
        
        // 1. Identificamos al usuario asociado (su cuenta)
        // Suponiendo que la relación en el modelo Alumno se llama 'usuario'
        if ($alumno->usuario) {
            $alumno->usuario->delete(); // Esto elimina la cuenta de acceso
        }

        // 2. Eliminamos al alumno (si usas borrado físico)
        $alumno->delete();

        return redirect()->back()->with('success', 'El alumno y su cuenta han sido eliminados correctamente.');
        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Ocurrió un error al intentar dar de baja al alumno.');
    }
}
}