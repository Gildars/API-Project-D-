<?php


namespace App\Modules\Character\UI\Http\CommandMappers;

use App\Http\Requests\Auth\StoreUserRequest;
use App\Modules\Character\Application\Commands\CreateCharacterCommand;
use App\User;
use Dingo\Api\Routing\Helpers;

class CreateCharacterCommandMapper
{
    use Helpers;
    public function map(StoreUserRequest $request, User $user): CreateCharacterCommand
    {
        return new CreateCharacterCommand(
            $request->input('name'),
            $request->input('gender'),
            $request->input('class_id'),
            $user->getId()
        );
    }
}
