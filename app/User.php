<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property Character character
 * @property integer id
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function character(): HasOne
    {
        return $this->hasOne(Character::class);
    }

    public function hasCharacter(): bool
    {
        return $this->character()->getQuery()->exists();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isCurrentAuthenticatedUser(): bool
    {
        return $this->getId() == Auth::id();
    }

    public function getCharacter(): Character
    {
        return $this->character;
    }

    public function hasThisCharacter(Character $character): bool
    {
        return $this->character->id === $character->getId();
    }

    public function updateLastUserActivity(): User
    {
        $expiresAt = Carbon::now()->addMinutes(5);

        Cache::put('last-user-activity-' . $this->id, true, $expiresAt);

        return $this;
    }

    public function isOnline(): bool
    {
        if(Cache::has('last-user-activity-' . $this->id)) {
            return true;
        }
        return false;
        //return Cache::has('last-user-activity-' . $this->id);
    }
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    /** Returns the value online or offline player.
     * @return bool
     */
    public function getIsOnlineAttribute()
    {
        $expiresAt = Carbon::now()->subMinute(5);
        return ($this->last_activity > $expiresAt) ? true : false;
    }
}
