<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Criterion extends Model
{
    protected $fillable = ['evento_id', 'name', 'max_score', 'description'];

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }
}
