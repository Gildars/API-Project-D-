<?php

namespace App\Modules\Character\Infrastructure\Repositories;

use App\CharacterClass as CharacterClassModel;
use App\Modules\Character\Application\Contracts\CharacterClassRepositoryInterface;
use App\Modules\Character\Domain\Attributes;
use App\Modules\Character\Domain\CharacterClass;

class CharacterClassRepository implements CharacterClassRepositoryInterface
{
    public function getOne(int $characterClassId): CharacterClass
    {
        /** @var CharacterClassModel $characterClass */
        $characterClass = CharacterClassModel::query()->findOrFail($characterClassId);

        return new CharacterClass(
            $characterClass->getId(),
            $characterClass->getStartingLocationId(),
            $characterClass->getName(),
            new Attributes([
                'strength' => $characterClass->getStrength(),
                'agility' => $characterClass->getAgility(),
                'stamina' => $characterClass->getStamina(),
                'intelligence' => $characterClass->getIntelligence(),
            ])
        );
    }
}
