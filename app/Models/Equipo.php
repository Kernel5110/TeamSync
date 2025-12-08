<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 
        'evento_id', 
        'submission_path',
        'github_repo',
        'github_pages',
        'project_name',
        'technologies',
        'project_description',
        'logo_path',
        'evidence_path',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function participantes()
    {
        return $this->hasMany(Participante::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
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
