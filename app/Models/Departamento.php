<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table      = 'departamento';
    protected $primaryKey = 'id_departamento';

    protected $fillable = ['nombre', 'edificio'];

    /**
     * Coordinador asignado a este departamento (si existe).
     */
    public function coordinadorDepartamento()
    {
        return $this->hasOne(CoordinadorDepartamento::class, 'id_departamento', 'id_departamento');
    }
}
