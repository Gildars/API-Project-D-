<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string name
 */
class CharacterClass extends Model
{
    const ATTRIBUTE_STRENGTH = 'strength';
    const ATTRIBUTE_AGILITY = 'agility';
    const ATTRIBUTE_STAMINA = 'stamina';
    const ATTRIBUTE_INTELLIGENCE = 'intelligence';

    const ATTRIBUTE_STARTING_LOCATION_ID = 'starting_location_id';
    const ATTRIBUTE_NAME = 'name';

    protected $table = 'character_classes';

    public function getId(): int
    {
        return $this->getKey();
    }

    public function getStartingLocationId(): string
    {
        return $this->{self::ATTRIBUTE_STARTING_LOCATION_ID};
    }

    public function getStrength(): int
    {
        return $this->{self::ATTRIBUTE_STRENGTH};
    }

    public function getAgility(): int
    {
        return $this->{self::ATTRIBUTE_AGILITY};
    }

    public function getStamina(): int
    {
        return $this->{self::ATTRIBUTE_STAMINA};
    }

    public function getIntelligence(): int
    {
        return $this->{self::ATTRIBUTE_INTELLIGENCE};
    }


    public function getName(): string
    {
        return $this->name;
    }

}
