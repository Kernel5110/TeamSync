<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Career extends Model
{
    /** @use HasFactory<\Database\Factories\CareerFactory> */
    use HasFactory;

    protected $table = 'carreras'; // Explicitly set table name if needed
    protected $fillable = ['nombre'];

    public function participants()
    {
        return $this->hasMany(Participant::class, 'carrera_id');
    }
}
