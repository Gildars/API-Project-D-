<?php


namespace App\Modules\User\UI\Http\CommandMappers;


use App\Http\Requests\Auth\StoreUserRequest;
use App\Modules\User\Application\Commands\CreateUserCommand;
use Illuminate\Http\Request;

class CreateUserCommandMapper
{
    public function map(StoreUserRequest $request): CreateUserCommand
    {
        return new CreateUserCommand(
            $request->input('name'),
            $request->input('email'),
            bcrypt($request->input('password'))
        );
    }
}
