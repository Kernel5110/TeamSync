<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Criterion extends Model
{
    protected $fillable = ['event_id', 'name', 'max_score', 'description'];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
