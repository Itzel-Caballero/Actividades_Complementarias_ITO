<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semestre extends Model
{
    protected $table      = 'semestre';
    protected $primaryKey = 'id_semestre';

  protected $fillable = [
    'año', 
    'periodo', 
    'fecha_inicio', 
    'fecha_fin', 
    'fecha_inicio_inscripciones', 
    'fecha_fin_inscripciones',
    'status', // <--- Agrega esto si no está
];

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_semestre');
    }
}