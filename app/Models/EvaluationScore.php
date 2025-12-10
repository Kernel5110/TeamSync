<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationScore extends Model
{
    protected $fillable = ['evaluation_id', 'criterion_id', 'score'];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function criterion()
    {
        return $this->belongsTo(Criterion::class);
    }
}
