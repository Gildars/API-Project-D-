<?php

namespace App;

use App\Traits\UsesStringId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string id
 * @property string name
 */
class Location extends Model
{
    use UsesStringId;

    /**
     * Get the characters at the location.
     *
     * @return HasMany
     */
    public function characters()
    {
        return $this->hasMany(Character::class);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
