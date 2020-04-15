<?php

namespace App\Modules\Character\Application\Contracts;

use App\Modules\Character\Domain\CharacterClass;

interface CharacterClassRepositoryInterface
{
    public function getOne(int $characterClassId): CharacterClass;
}
