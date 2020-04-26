<?php

namespace App\Http\Api\Controllers;

use App\Battle;
use App\Http\Controllers\BaseController;

class BattleController extends BaseController
{

    public function __construct()
    {
    }

    public function show(string $battleId)
    {
        $battle = Battle::query()->findOrFail($battleId);

        return view('battle.show', compact('battle'));
    }
}
