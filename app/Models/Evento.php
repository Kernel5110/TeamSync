<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    /** @use HasFactory<\Database\Factories\EventoFactory> */
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'fecha_inicio', 'fecha_fin', 'ubicacion', 'capacidad', 'problem_statement', 'categoria'];

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
}
