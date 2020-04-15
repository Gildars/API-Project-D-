<?php


namespace App\Modules\Character\Application\Factories;

use App\Modules\Character\Application\Commands\CreateCharacterCommand;
use App\Modules\Character\Domain\CharacterId;
use App\Modules\Character\Domain\CharacterClass;
use App\Modules\Equipment\Domain\Inventory;
use App\Modules\Character\Domain\Statistics;
use App\Modules\Character\Domain\Attributes;
use App\Modules\Character\Domain\Character;
use App\Modules\Character\Domain\Gender;
use App\Modules\Character\Domain\HitPoints;
use App\Modules\Character\Domain\Money;
use App\Modules\Character\Domain\Reputation;


class CharacterFactory
{
    public function create(
        CharacterId $characterId,
        CreateCharacterCommand $command,
        CharacterClass $characterClass,
        Inventory $inventory
    ): Character {
        return new Character(
            $characterId,
            $characterClass->getId(),
            1,
            $characterClass->getStartingLocationId(),
            $command->getName(),
            new Gender($command->getGender()),
            0,
            new Money(0),
            new Reputation(0),
            new Attributes([
                'strength' => $characterClass->getStrength(),
                'agility' => $characterClass->getAgility(),
                'stamina' => $characterClass->getStamina(),
                'intelligence' => $characterClass->getIntelligence(),
                'unassigned' => 0,
            ]),
            HitPoints::byCharacterClass($characterClass),
            new Statistics([
                'battlesLost' => 0,
                'battlesWon' => 0,
            ]),
            $inventory,
            $command->getUserId()
        );
    }
}
