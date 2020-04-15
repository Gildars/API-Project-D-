<?php


namespace App\Modules\User\UI\Http\CommandMappers;


use App\Modules\User\Application\Commands\CreateUserCommand;
use Illuminate\Http\Request;

class CreateUserCommandMapper
{
    public function map(Request $request): CreateUserCommand
    {
        return new CreateUserCommand(
            $request->input('name'),
            $request->input('email'),
            bcrypt($request->input('password'))
        );
    }
}
