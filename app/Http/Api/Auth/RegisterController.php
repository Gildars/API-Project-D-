<?php

namespace App\Http\Api\Auth;

use App\CharacterClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisterController extends Controller
{
    use RegistersUsers;
    use Helpers;

    public function register(Request $request)
    {

        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            throw new StoreResourceFailedException("Validation Error",
                $validator->errors());
        }

        $user = $this->create($request->all());

        if ($user) {

            $token = JWTAuth::fromUser($user);

            return $this->response->array([
                "token"       => $token,
                "message"     => "User created",
                "status_code" => 201,
            ]);
        } else {
            return $this->response->error("User Not Found...", 404);
        }
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email'    => 'required|email|max:255|unique:user',
            'password' => 'required|min:6|max:28',
        ], [],
            $this->attributes());
    }

    protected function validatorCharacter(array $data)
    {
        $classes = CharacterClass::all('id')->getQueueableIds();
        return Validator::make($data, [
            'name'  => 'required|alpha|min:2|max:12|unique:user',
            'class' => [
                'required',
                Rule::in($classes),
            ],
        ]);
    }

    protected function attributes()
    {
        return [
            'password' => 'пароль',
            'token'    => 'токен',
        ];
    }

    protected function create(array $data)
    {
        return User::create([
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    protected function createCharacter(Request $request)
    {
        $this->middleware('api.auth');
        $validator = $this->validatorCharacter($request->all());
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Validation Error',
                $validator->errors());
        }
        $user = $this->auth()->user();
        if ( ! $user->name) {
            $user->name = $request['name'];
            $user->update();

            return $this->response->array([
                $user,
            ]);
        }
    }

}
