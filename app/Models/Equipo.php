<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'evento_id', 'submission_path'];

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function participantes()
    {
        return $this->hasMany(Participante::class);
    }
}
