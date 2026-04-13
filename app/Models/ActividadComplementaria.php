<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadComplementaria extends Model
{
    protected $table      = 'actividad_complementaria';
    protected $primaryKey = 'id_actividad';

    protected $fillable = [
        'nombre', 'descripcion', 'id_categoria',
        'id_departamento', 'requisitos', 'nivel_actividad',
        'disponible', 'creditos',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_actividad');
    }

    public function carreras()
    {
        return $this->belongsToMany(Carrera::class, 'actividad_carrera', 'id_actividad', 'id_carrera');
    }
}