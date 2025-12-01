<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participante extends Model
{
    /** @use HasFactory<\Database\Factories\ParticipanteFactory> */
    use HasFactory;

    protected $fillable = [
        'usuario_id',
        'carrera_id',
        'num_control',
        'institucion',
        'equipo_id',
        'rol',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }
}
