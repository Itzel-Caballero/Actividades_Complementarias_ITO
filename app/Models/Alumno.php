<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $table      = 'alumno';
    protected $primaryKey = 'id_alumno';

    protected $fillable = [
        'id_alumno',  'id_carrera', 'semestre_cursando', 'creditos_acumulados',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_alumno');
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera');
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'id_alumno');
    }
}