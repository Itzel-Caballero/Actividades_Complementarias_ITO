<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $table      = 'carrera';
    protected $primaryKey = 'id_carrera';

    protected $fillable = ['nombre'];

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'id_carrera');
    }

    public function actividades()
    {
        return $this->belongsToMany(ActividadComplementaria::class, 'actividad_carrera', 'id_carrera', 'id_actividad');
    }
}