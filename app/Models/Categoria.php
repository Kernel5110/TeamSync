<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $fillable = ['name'];

    public function eventos()
    {
        return $this->belongsToMany(Event::class, 'category_event', 'category_id', 'event_id');
    }
    //
}
