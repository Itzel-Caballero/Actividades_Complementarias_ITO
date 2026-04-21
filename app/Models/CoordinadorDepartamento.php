<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoordinadorDepartamento extends Model
{
    protected $table    = 'coordinador_departamento';
    protected $fillable = ['id_usuario', 'id_departamento'];

    /**
     * El usuario (coordinador) asignado.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }

    /**
     * El departamento al que está asignado.
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento', 'id_departamento');
    }
}
