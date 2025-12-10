<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $table = 'teams'; // Explicitly set table name if needed

    protected $fillable = [
        'name', 
        'event_id', 
        'submission_path',
        'github_repo',
        'github_pages',
        'project_name',
        'technologies',
        'project_description',
        'logo_path',
        'evidence_path',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function participants()
    {
        return $this->hasMany(Participant::class, 'team_id');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'team_id');
    }

    public function getProgressAttribute()
    {
        $fields = [
            'project_name',
            'technologies',
            'github_repo',
            'project_description',
            'evidence_path',
        ];

        $completed = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $completed++;
            }
        }

        return count($fields) > 0 ? round(($completed / count($fields)) * 100) : 0;
    }
}
