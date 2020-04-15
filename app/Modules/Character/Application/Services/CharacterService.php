<?php


namespace App\Modules\Character\Application\Services;


//use App\Modules\Battle\Application\Contracts\BattleRepositoryInterface;
//use App\Modules\Battle\Domain\Battle;
//use App\Modules\Battle\Domain\BattleId;
//use App\Modules\Battle\Domain\BattleRounds;
use App\Modules\Character\Application\Contracts\CharacterClassRepositoryInterface;
use App\Modules\Character\Domain\CharacterId;
use App\Modules\Character\Application\Contracts\CharacterRepositoryInterface;
use App\Modules\Character\Domain\Character;
use App\Modules\Character\Application\Commands\AttackCharacterCommand;
use App\Modules\Character\Application\Commands\CreateCharacterCommand;
use App\Modules\Character\Application\Commands\IncreaseAttributeCommand;
use App\Modules\Character\Application\Commands\MoveCharacterCommand;
use App\Modules\Character\Application\Factories\CharacterFactory;
use App\Modules\Equipment\Application\Commands\CreateInventoryCommand;
use App\Modules\Equipment\Application\Services\InventoryService;
use App\Modules\Level\Application\Services\LevelService;
use Illuminate\Support\Facades\DB;

class CharacterService
{
    /**
     * @var CharacterFactory
     */
    private $characterFactory;
    /**
     * @var CharacterRepositoryInterface
     */
    private $characterRepository;
    /**
     * @var CharacterClassRepositoryInterface
     */
    private $characterClassRepository;
    /**
     * @var BattleRepositoryInterface
     */
   // private $battleRepository;
    /**
     * @var LevelService
     */
    private $levelService;
    /**
     * @var InventoryService
     */
    private $inventoryService;

    public function __construct(
        CharacterFactory $characterFactory,
        CharacterRepositoryInterface $characterRepository,
        CharacterClassRepositoryInterface $characterClassRepository,
        //BattleRepositoryInterface $battleRepository,
        LevelService $levelService,
        InventoryService $inventoryService
    )
    {
        $this->characterFactory = $characterFactory;
        $this->characterRepository = $characterRepository;
        $this->characterClassRepository = $characterClassRepository;
        //$this->battleRepository = $battleRepository;
        $this->levelService = $levelService;
        $this->inventoryService = $inventoryService;
    }

    public function create(CreateCharacterCommand $command): Character
    {
        $characterId = $this->characterRepository->nextIdentity();

        $characterClass = $this->characterRepository->getOne($command->getCharacterClassId());
        $inventory = $this->inventoryService->create(new CreateInventoryCommand($characterId));

        $character = $this->characterFactory->create($characterId, $command, $characterClass, $inventory);

        $this->characterRepository->add($character);

        return $character;
    }

    public function increaseAttribute(IncreaseAttributeCommand $command): void
    {
        $character = $this->characterRepository->getOne($command->getCharacterId());

        $character->applyAttributeIncrease($command->getAttribute());

        $this->characterRepository->update($character);
    }

    public function move(MoveCharacterCommand $command): void
    {
        $character = $this->characterRepository->getOne($command->getCharacterId());

        $character->setLocationId($command->getLocationId());

        $this->characterRepository->update($character);
    }

    public function attack(AttackCharacterCommand $command): BattleId
    {
        return DB::transaction(function () use ($command) {

            $attacker = $this->characterRepository->getOne($command->getAttackerId());
            $defender = $this->characterRepository->getOne($command->getDefenderId());

            $battleId = $this->battleRepository->nextIdentity();

            $battle = new Battle(
                $battleId,
                $defender->getLocationId(),
                $attacker,
                $defender,
                new BattleRounds(),
                0
            );

            $battle->execute();

            $victor = $battle->getVictor();
            $loser = $battle->getLoser();

            $victor->incrementWonBattles();
            $loser->incrementLostBattles();

            $victor->addXp($battle->getVictorXpGained());

            $newLevel = $this->levelService->getLevelByXp($victor->getXp());

            $victor->updateLevel($newLevel->getId());

            $this->characterRepository->update($victor);
            $this->characterRepository->update($loser);
            $this->battleRepository->add($battle);

            return $battleId;
        });
    }

    public function updateProfilePicture(Image $picture): void
    {
        $character = $this->characterRepository->getOne($picture->getCharacterId());

        $character->setProfilePictureId($picture->getId());

        $this->characterRepository->update($character);
    }

    public function removeProfilePicture(CharacterId $characterId): void
    {
        $character = $this->characterRepository->getOne($characterId);

        $character->removeProfilePicture();

        $this->characterRepository->update($character);
    }
}
