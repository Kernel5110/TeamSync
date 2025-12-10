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
        'is_conflict',
        'finalized_at',
        'private_notes',
    ];

    protected $casts = [
        'finalized_at' => 'datetime',
        'is_conflict' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'equipo_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'evento_id');
    }

    public function scores()
    {
        return $this->hasMany(EvaluationScore::class);
    }
}
