<?php

namespace App\Http\Api\Controllers;

use App\Character;
use App\Http\Requests\Character\CharacterRequest;
use App\Modules\Character\Application\Services\CharacterService;
use App\Modules\Character\UI\Http\CommandMappers\AttackCharacterCommandMapper;
use App\Http\Requests\Character\UpdateCharacterAttributeRequest;
use App\Modules\Character\UI\Http\CommandMappers\IncreaseAttributeCommandMapper;
use App\Modules\Character\UI\Http\CommandMappers\MoveCharacterCommandMapper;
use Dingo\Api\Auth\Auth;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations\OpenApi;
use Symfony\Component\HttpFoundation\Response;

class CharacterController
{
    use Helpers;
    /**
     * @var CharacterService
     */
    private $characterService;

    /**
     * CharacterController constructor.
     *
     * @param CharacterService $characterService
     */
    public function __construct(CharacterService $characterService)
    {
        $this->characterService = $characterService;
    }

    /**
     * @OA\Get(
     *     path="/character/id/{id}",
     *     description="Находит данные игрока по id.",
     *     tags={"character"},
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     * @OA\Schema(
     *           type="integer",
     *           format="int32"
     *         )
     * ),
     * @OA\Response(response="200", description="Возвращает найденного персонажа."),
     * @OA\Response(response="404", description="Персонаж не найден."),
     * )
     */
    public function getCharacterById(string $characterId): JsonResponse
    {
        $character = Character::query()->find($characterId);
        if (!$character) {
            return response()->json(['message' => 'Character Not Found.'], 404);
        }
        return response()->json(['character' => $character], 200);
    }

    /**
     * @OA\Get(
     *     path="/character/name/{name}",
     *     description="Находит данные игрока по имени.",
     *     tags={"character"},
     * @OA\Parameter(
     *     name="name",
     *     in="path",
     *     required=true,
     * @OA\Schema(
     *           type="string"
     *         )
     * ),
     * @OA\Response(response="200", description="Возвращает найденного персонажа."),
     * @OA\Response(response="404", description="Персонаж не найден."),
     * )
     */
    public function getCharacterByName(CharacterRequest $request): JsonResponse
    {
        $character = Character::query()->where('name', '=', $request->name)->get();
        if (!$character) {
            return response()->json(['message' => 'Character Not Found.'], 404);
        }
        return response()->json(['character' => $character], 200);
    }

    /**
     * @OA\Put(
     *     path="/character",
     *     description="Добавляет +1 атрибут харктеристики.",
     *     tags={"character"},
     * @OA\RequestBody(
     *     required=true
     *         ),
     *
     * @OA\Response(response="200", description="Возвращает найденного персонажа."),
     * @OA\Response(response="404", description="Персонаж не найден."),
     * )
     */
    public function update(
        UpdateCharacterAttributeRequest $request,
        IncreaseAttributeCommandMapper $commandMapper
    ): Response {
        $increaseAttributeCommand = $commandMapper->map(auth()->user()->character->id, $request);

        if (!$this->characterService->increaseAttribute($increaseAttributeCommand)) {
            return response()->json(
                [
                    'message' => 'Error.'
                ],
                422);
        } else {
            return response()->json(
                [
                    'status' => ucfirst($increaseAttributeCommand->getAttribute()) . ' + 1'
                ],
                200);
        }
    }

    public function move(
        MoveCharacterCommandMapper $commandMapper,
        string $characterId,
        string $locationId
    ): Response {
        $moveCharacterCommand = $commandMapper->map($characterId, $locationId);

        $this->characterService->move($moveCharacterCommand);

        return redirect()->route('location.show', $locationId);
    }

    public function attack(
        string $defenderId,
        Request $request,
        AttackCharacterCommandMapper $commandMapper
    ): Response {

        $attackCharacterCommand = $commandMapper->map($request, $defenderId);

        $battleId = $this->characterService->attack($attackCharacterCommand);

        return redirect()->route('battle.show', $battleId->toString());
    }
}
