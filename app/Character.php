<?php

namespace App;

use App\Modules\Equipment\Domain\ItemStatus;
use App\Modules\Equipment\Domain\ItemType;
use App\Traits\UsesStringId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property User user
 * @property Location location
 * @property string id
 * @property integer hit_points
 * @property integer xp
 * @property integer available_attribute_points
 * @property integer battles_won
 * @property integer battles_lost
 * @property integer strength
 * @property integer agility
 * @property integer constitution
 * @property integer intelligence
 * @property integer charisma
 * @property string location_id
 * @property string gender
 * @property int total_hit_points
 * @property int victor_xp_gained
 * @property string name
 * @property int level_id
 * @property string profile_picture_id
 * @property Inventory inventory
 */
class Character extends Model
{
    use UsesStringId;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }


    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function attacks(): HasMany
    {
        return $this->hasMany(Battle::class, 'attacker_id');
    }

    public function defends(): HasMany
    {
        return $this->hasMany(Battle::class, 'defender_id');
    }

    public function battles(): HasMany
    {
        return $this->hasMany(Battle::class, 'defender_id');
    }


    public function isYou(): bool
    {
        return $this->isPlayerCharacter() && $this->user->isCurrentAuthenticatedUser();
    }

    public function isPlayerCharacter(): bool
    {
        return $this->user !== null;
    }

    public function isNPC(): bool
    {
        return $this->user === null;
    }

    public function hasProfilePicture(): bool
    {
        return $this->profilePicture()->exists();
    }

    public function isOnline(): bool
    {
        if($this->isNPC()) {
            return true;
        }

        return $this->user->isOnline();
    }

    public function getRaceName(): string
    {
        return $this->race->getName();
    }

    public function getLevelNumber():int
    {
        return $this->level_id;
    }

    public function getLocationName():string
    {
        return $this->location->getName();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isAlive(): bool
    {
        return $this->hit_points > 0;
    }

    public function getStrength(): int
    {
        return $this->strength;
    }

    public function getAgility(): int
    {
        return $this->agility;
    }

    public function getConstitution(): int
    {
        return $this->constitution;
    }

    public function getIntelligence(): int
    {
        return $this->intelligence;
    }

    public function getCharisma(): int
    {
        return $this->charisma;
    }

    public function getLocationId(): string
    {
        return $this->location_id;
    }

    public function getHitPoints(): int
    {
        return $this->hit_points;
    }

    public function getTotalHitPoints(): int
    {
        return $this->total_hit_points;
    }

    public function getUserId()
    {
        return $this->user ? $this->user->getId() : null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGender(): string
    {
        return $this->gender;
    }


    public function getXp(): int
    {
        return $this->xp;
    }

    public function getAvailableAttributePoints(): int
    {
        return $this->available_attribute_points;
    }

    public function getBattlesLost(): int
    {
        return $this->battles_lost;
    }

    public function getBattlesWon(): int
    {
        return $this->battles_won;
    }

    public function getHeadGearItem()
    {
        return $this->inventory->items()
            ->where('type', ItemType::HEAD_GEAR)
            ->wherePivot('status', ItemStatus::EQUIPPED)
            ->first()
        ;
    }

    public function getBodyArmorItem()
    {
        return $this->inventory->items()
            ->where('type', ItemType::BODY_ARMOR)
            ->wherePivot('status', ItemStatus::EQUIPPED)
            ->first()
        ;
    }

    public function getMainHandItem()
    {
        return $this->inventory->items()
            ->where('type', ItemType::MAIN_HAND)
            ->wherePivot('status', ItemStatus::EQUIPPED)
            ->first()
        ;
    }

    public function getOffHandItem()
    {
        return $this->inventory->items()
            ->where('type', ItemType::OFF_HAND)
            ->wherePivot('status', ItemStatus::EQUIPPED)
            ->first()
        ;
    }
}
