<?php

namespace App\Http\Api\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Requests\User\StoreStatUserRequest;
use App\Repositories\StatRepository;
use Dingo\Api\Routing\Helpers;
use Illuminate\Support\Facades\Auth;

class StatController extends BaseController
{
    use Helpers;


    protected $friendRepository;

    public function __construct(StatRepository $statRepository)
    {
        parent::__construct();
        $this->middleware('api.auth');
        $this->statRepository = $statRepository;
    }

    /**
     * @OA\Get(
     *     path="/stat/",
     *     description="Возвращает характеристики персонажа.",
     *     tags={"stat"},
     *
     * @OA\Response(response="200", description="Характеристики персонажа."),
     * @OA\Response(response="401", description="Unauthorized."),
     * )
     */
    public function getStats()
    {
        if ($stats = $this->statRepository->getStatsByUserId(Auth::id())) {
            return response(
                [
                    $stats
                ], 200
            );
        }
    }

    /**
     * @OA\Post(
     *     path="/stat/",
     *     description="Увеличивает характеристики персонажа.",
     *     tags={"stat"},
     *
     * @OA\Response(response="200", description="Характеристики персонажа увеличены."),
     * @OA\Response(response="422", description="Переданы некоректные значения характеристик."),
     * @OA\Response(response="401", description="Unauthorized.")
     * )
     */
    public function increaseStats(StoreStatUserRequest $request)
    {
        if (!$this->statRepository->increaseStats($request)) {
            return response()->json(
                [
                    'message' => 'Request rejected.'
                ],
                422
            );
        }
        return response()->json(
            [
                'message' => 'Stats have been successfully increased.'
            ],
            200
        );

    }
}
