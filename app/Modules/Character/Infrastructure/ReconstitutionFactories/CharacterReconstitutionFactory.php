<?php


namespace App\Modules\Character\Infrastructure\ReconstitutionFactories;

use App\Modules\Character\Domain\CharacterId;
use App\Modules\Equipment\Infrastructure\ReconstitutionFactories\InventoryReconstitutionFactory;
use App\Modules\Character\Domain\Attributes;
use App\Modules\Character\Domain\Character;
use App\Modules\Character\Domain\Gender;
use App\Modules\Character\Domain\Statistics;
use App\Modules\Character\Domain\Money;
use App\Modules\Character\Domain\HitPoints;
use App\Modules\Character\Domain\Reputation;
use App\Character as CharacterModel;


class CharacterReconstitutionFactory
{
    /**
     * @var InventoryReconstitutionFactory
     */
    private $inventoryReconstitutionFactory;

    public function __construct(InventoryReconstitutionFactory $inventoryReconstitutionFactory)
    {
        $this->inventoryReconstitutionFactory = $inventoryReconstitutionFactory;
    }

    public function reconstitute(CharacterModel $characterModel): Character
    {
        $inventory = $this->inventoryReconstitutionFactory->reconstitute($characterModel->inventory);


        $character = new Character(
            CharacterId::fromString($characterModel->getId()),
            $characterModel->getCharacterId(),
            $characterModel->getLevelNumber(),
            $characterModel->getLocationId(),
            $characterModel->getName(),
            new Gender($characterModel->getGender()),
            $characterModel->getXp(),
            new Money(0),
            new Reputation(0),
            new Attributes([
                'strength' => $characterModel->getStrength(),
                'agility' => $characterModel->getAgility(),
                'stamina' => $characterModel->getStamina(),
                'intelligence' => $characterModel->getIntelligence(),
                'unassigned' => $characterModel->getAvailableAttributePoints(),
            ]),
            new HitPoints(
                $characterModel->getHitPoints(),
                $characterModel->getTotalHitPoints()
            ),
            new Statistics([
                'battlesLost' => $characterModel->getBattlesLost(),
                'battlesWon' => $characterModel->getBattlesWon(),
            ]),
            $inventory,
            $characterModel->getUserId()
        );

        return $character;
    }
}
