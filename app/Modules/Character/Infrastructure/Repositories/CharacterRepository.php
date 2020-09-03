<?php

namespace App\Modules\Character\Infrastructure\Repositories;

use App\Modules\Character\Application\Contracts\CharacterRepositoryInterface;
use App\Modules\Character\Domain\Character;
use App\Character as CharacterModel;
use App\Modules\Character\Domain\CharacterId;
use App\Modules\Character\Infrastructure\ReconstitutionFactories\CharacterReconstitutionFactory;
use App\Traits\GeneratesUuid;
use Exception;

class CharacterRepository implements CharacterRepositoryInterface
{
    use GeneratesUuid;

    /**
     * @var CharacterReconstitutionFactory
     */
    private $characterReconstitutionFactory;

    public function __construct(CharacterReconstitutionFactory $characterReconstitutionFactory)
    {
        $this->characterReconstitutionFactory = $characterReconstitutionFactory;
    }

    /**
     * @return CharacterId
     *
     * @throws Exception
     */
    public function nextIdentity(): CharacterId
    {
        return CharacterId::fromString($this->generateUuid());
    }

    public function add(Character $character): void
    {
        /** @var CharacterModel $characterModel */
        CharacterModel::query()->create([
            'id' => $character->getId()->toString(),
            'user_id' => $character->getUserId(),

            'name' => $character->getName(),
            'gender' => $character->getGender()->getValue(),

            'xp' => $character->getXp(),
            'level_id' => $character->getLevelNumber(),
            'money' => $character->getMoney()->getValue(),
            'reputation' => $character->getReputation()->getValue(),

            'strength' => $character->getStrength(),
            'agility' => $character->getAgility(),
            'stamina' => $character->getStamina(),
            'intelligence' => $character->getIntelligence(),

            'hit_points' => $character->getHitPoints(),
            'total_hit_points' => $character->getTotalHitPoints(),

            'character_class_id' => $character->getCharacterClassId(),
            'location_id' => $character->getLocationId(),

            'battles_won' => $character->getBattlesWon(),
            'battles_lost' => $character->getBattlesLost(),
        ]);
    }

    public function getOne(CharacterId $characterId): Character
    {
        /** @var CharacterModel $characterModel */
        $characterModel = CharacterModel::query()->with('inventory')->findOrFail($characterId->toString());

        return $this->characterReconstitutionFactory->reconstitute($characterModel);
    }

    public function update(Character $character): bool
    {
        /** @var CharacterModel $characterModel */
        $characterModel = CharacterModel::query()->findOrFail($character->getId()->toString());

       return $characterModel->update([
            'name' => $character->getName(),
            'gender' => $character->getGender()->getValue(),

            'xp' => $character->getXp(),
            'level_id' => $character->getLevelNumber(),
            'money' => $character->getMoney()->getValue(),
            'reputation' => $character->getReputation()->getValue(),

            'strength' => $character->getStrength(),
            'agility' => $character->getAgility(),
            'stamina' => $character->getStamina(),
            'intelligence' => $character->getIntelligence(),
            'available_attribute_points' => $character->getUnassignedAttributePoints(),

            'hit_points' => $character->getHitPoints(),
            'total_hit_points' => $character->getTotalHitPoints(),

            'battles_won' => $character->getBattlesWon(),
            'battles_lost' => $character->getBattlesLost(),

            'location_id' => $character->getLocationId(),
        ]);
    }
}