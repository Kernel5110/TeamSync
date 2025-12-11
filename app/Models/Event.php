<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventoFactory> */
    use HasFactory;

    protected $table = 'events';

    protected $fillable = ['name', 'description', 'image_path', 'starts_at', 'ends_at', 'location', 'capacity', 'problem_statement', 'status_manual'];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function teams()
    {
        return $this->hasMany(Team::class, 'event_id');
    }

    public function judges()
    {
        return $this->belongsToMany(User::class, 'event_judge', 'event_id', 'user_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Categoria::class, 'category_event', 'event_id', 'category_id');
    }

    public function criteria()
    {
        return $this->hasMany(Criterion::class, 'event_id');
    }

    public function getStatusAttribute()
    {
        if ($this->status_manual) {
            return $this->status_manual;
        }

        $now = now('America/Mexico_City');
        
        if ($now->lessThan($this->starts_at)) {
            return 'Próximo';
        } elseif ($now->between($this->starts_at, $this->ends_at)) {
            return 'En Curso';
        } else {
            return 'Finalizado';
        }
    }

    public function syncCriteria(array $criteriaData)
    {
        $sentIds = [];
        foreach ($criteriaData as $criterion) {
            if (!empty($criterion['name'])) {
                if (isset($criterion['id'])) {
                    $crit = $this->criteria()->find($criterion['id']);
                    if ($crit) {
                        $crit->update([
                            'name' => $criterion['name'],
                            'max_score' => $criterion['max_score'] ?? 10,
                            'description' => $criterion['description'] ?? null,
                        ]);
                        $sentIds[] = $crit->id;
                    }
                } else {
                    $newCrit = $this->criteria()->create([
                        'name' => $criterion['name'],
                        'max_score' => $criterion['max_score'] ?? 10,
                        'description' => $criterion['description'] ?? null,
                    ]);
                    $sentIds[] = $newCrit->id;
                }
            }
        }
        // Delete criteria not in request
        $this->criteria()->whereNotIn('id', $sentIds)->delete();
    }

    /**
     * Get the ranking of teams for this event based on evaluations.
     */
    public function getRanking()
    {
        return $this->teams->map(function ($team) {
            $evaluations = $team->evaluations;
            if ($evaluations->isEmpty()) {
                $team->total_score = 0;
            } else {
                // Return average of (Sum of scores from EvaluationScore)
                // We assume each evaluation has many scores (one per criterion)
                // The total score for an evaluation is the sum of its criterion scores.
                // The team score is the average of these totals (one per judge).
                
                $team->total_score = $evaluations->avg(function ($eval) {
                     return $eval->scores->sum('score');
                });
            }
            return $team;
        })->sortByDesc('total_score')->values();
    }
}
