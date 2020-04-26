<?php

namespace App\Http\Api\Controllers;

use App\Character;
use App\Http\Controllers\BaseController;

class CharacterBattleController extends BaseController
{
    public function index(string $characterId)
    {
        $character = Character::query()->findOrFail($characterId);

        return view('character.battle.index', compact('character'));
    }
}
