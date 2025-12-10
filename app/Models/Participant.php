<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    /** @use HasFactory<\Database\Factories\ParticipantFactory> */
    use HasFactory;

    protected $table = 'participantes'; // Explicitly set table name if needed

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

    public function career()
    {
        return $this->belongsTo(Career::class, 'carrera_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'equipo_id');
    }
}
