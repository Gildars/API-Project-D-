<?php

namespace App\Http\Api\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Item\ItemRequest;
use App\Modules\Equipment\Application\Services\InventoryService;
use App\Modules\Equipment\UI\Http\CommandMappers\EquipItemCommandMapper;
use Dingo\Api\Routing\Helpers;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends BaseController
{
    use Helpers;
    /**
     * @var InventoryService
     */
    private $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        parent::__construct();
        $this->middleware('api.auth');
        $this->inventoryService = $inventoryService;
    }

    /**
     * @OA\Get(
     *     path="/inventory",
     *     description="Инвентарь.",
     *     tags={"inventory"},
     * @OA\Response(response="200", description="Содержимое инвентаря.")
     * )
     */
    public function getInventory(): JsonResponse
    {
        $inventory = $this->auth->user()->character->inventory->items;
        return response()->json(['inventory' => $inventory], 200);
    }

    /**
     * @OA\Post(
     *     path="/inventory/item/{item}/equip",
     *     description="Одевает предмет на персонажа.",
     *     tags={"inventory"},
     *     @OA\Parameter(
     *     name="item",
     *     in="query",
     *     required=true,
     * @OA\Schema(
     *             type="string"
     *         )
     * ),
     * @OA\Response(response="200", description="Предмет одет на пресонажа."),
     * @OA\Response(response="422", description="Ошибка. Предмет не может быть одет.")
     * )
     */
    public function equipItem(ItemRequest $request, EquipItemCommandMapper $commandMapper): JsonResponse
    {
        $equipItemCommand = $commandMapper->map($request);

        try {

            DB::transaction(function () use ($equipItemCommand) {
                $this->inventoryService->equipItem($equipItemCommand);
            });

        } catch (Exception $exception) {

            return response()->json([
                'message' => 'Error equipping item'
            ],422);
        }

        return response()->json(['message' => 'Item equipped'], 200);
    }

    /**
     * @OA\Post(
     *     path="/inventory/item/{item}/un-equip",
     *     description="Снимает предмет с персонажа.",
     *     tags={"inventory"},
     *     @OA\Parameter(
     *     name="item",
     *     in="query",
     *     required=true,
     * @OA\Schema(
     *             type="string"
     *         )
     * ),
     * @OA\Response(response="200", description="Предмет снят с пресонажа."),
     * @OA\Response(response="422", description="Ошибка. Предмет не может снят.")
     * )
     */
    public function unEquipItem(ItemRequest $request, EquipItemCommandMapper $commandMapper): JsonResponse
    {
        $equipItemCommand = $commandMapper->map($request);

        try {

            DB::transaction(function () use ($equipItemCommand) {
                $this->inventoryService->unEquipItem($equipItemCommand);
            });

        } catch (Exception $exception) {

            return response()->json([
                'message' => 'Error un-equipping item'
            ],422);
        }

        return response()->json(['message' => 'Item un-equipped'],200);
    }
}
