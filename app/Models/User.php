<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasRoles, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
        'expertise',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function judgeEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_judge', 'user_id', 'event_id');
    }

    public function participant(): HasOne
    {
        return $this->hasOne(Participant::class, 'user_id')->latest('created_at');
    }

    public function participants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Participant::class, 'user_id');
    }

    /**
     * Get the event the user is currently participating in, if it is active.
     */
    public function getActiveEventAttribute(): ?Event
    {
        $participants = $this->participants; // Use the collection

        foreach ($participants as $participant) {
            if ($participant->team && $participant->team->event) {
                $event = $participant->team->event;
                if ($event->status === 'En Curso' || $event->status === 'Próximo') {
                    return $event;
                }
            }
        }

        return null;
    }
}
