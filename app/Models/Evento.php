<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    /** @use HasFactory<\Database\Factories\EventoFactory> */
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'fecha_inicio', 'fecha_fin', 'start_time', 'ubicacion', 'capacidad', 'problem_statement', 'categoria'];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function equipos()
    {
        return $this->hasMany(Equipo::class);
    }

    public function jueces()
    {
        return $this->belongsToMany(User::class, 'evento_juez', 'evento_id', 'user_id');
    }

    public function getStatusAttribute()
    {
        $now = now('America/Mexico_City');
        $startDateTime = $this->fecha_inicio->copy()->setTimeFromTimeString($this->start_time ?? '00:00:00');
        $endDateTime = $this->fecha_fin->copy()->endOfDay();

        if ($now->lessThan($startDateTime)) {
            return 'Próximo';
        } elseif ($now->between($startDateTime, $endDateTime)) {
            return 'En Curso';
        } else {
            return 'Finalizado';
        }
    }
}
