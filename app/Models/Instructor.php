<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    protected $table      = 'instructor';
    protected $primaryKey = 'id_instructor';
    public    $incrementing = false;  // la PK viene del id de USUARIO

    protected $fillable = ['id_instructor', 'id_departamento', 'especialidad'];

    public function usuario()
    {
        // id_instructor de la tabla instructor = id de la tabla USUARIO
        return $this->belongsTo(User::class, 'id_instructor', 'id');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_instructor');
    }
}