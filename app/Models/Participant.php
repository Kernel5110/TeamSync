<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    /** @use HasFactory<\Database\Factories\ParticipantFactory> */
    use HasFactory;

    protected $table = 'participants'; // Explicitly set table name if needed

    protected $fillable = [
        'user_id',
        'career_id',
        'control_number',
        'institution',
        'team_id',
        'rol',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function career()
    {
        return $this->belongsTo(Career::class, 'career_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
