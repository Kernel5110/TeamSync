<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
        'user_id',
        'equipo_id',
        'evento_id',
        'score_innovation',
        'score_social_impact',
        'score_technical_viability',
        'comments',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }
}
