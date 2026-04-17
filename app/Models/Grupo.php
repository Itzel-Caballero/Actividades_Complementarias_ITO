<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table      = 'grupo';
    protected $primaryKey = 'id_grupo';

    protected $fillable = [
        'id_actividad', 'id_semestre', 'id_instructor',
        'id_ubicacion', 'grupo', 'cupo_minimo', 'cupo_maximo', 'cupo_ocupado',
        'modalidad', 'materiales_requeridos', 'estatus',
        'fecha_inicio', 'fecha_fin',
    ];

    public function actividad()
    {
        return $this->belongsTo(ActividadComplementaria::class, 'id_actividad');
    }

    public function semestre()
    {
        return $this->belongsTo(Semestre::class, 'id_semestre');
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'id_instructor');
    }

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class, 'id_ubicacion');
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class, 'id_grupo');
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'id_grupo');
    }
}