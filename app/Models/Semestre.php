<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'hora_inicio_inscripciones',
        'fecha_fin_inscripciones',
        'hora_fin_inscripciones',
        'status',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────
    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_semestre');
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    /** Etiqueta legible: "Enero–Junio 2026" */
    public function getLabelAttribute(): string
    {
        return $this->periodo == 1
            ? "Enero–Junio {$this->año}"
            : "Agosto–Diciembre {$this->año}";
    }

    /** Retorna true si la fecha_fin ya pasó (hoy > fecha_fin) */
    public function haVencido(): bool
    {
        return Carbon::today()->gt(Carbon::parse($this->fecha_fin));
    }

    /**
     * Cierra automáticamente el semestre si su fecha_fin ya pasó.
     * Llama esto desde el constructor del controller o desde un Command.
     */
    public static function cerrarVencidos(): void
    {
        self::where('status', 'activo')
            ->where('fecha_fin', '<', Carbon::today()->toDateString())
            ->update(['status' => 'inactivo']);
    }
}
