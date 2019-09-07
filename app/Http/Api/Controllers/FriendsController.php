<?php

namespace App\Http\Api\Controllers;

use App\Friend;
use App\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class FriendsController extends Controller
{
    use Helpers;

    public function __construct()
    {
        $this->middleware('api.auth');
    }

    public function index()
    {
        $user = $this->auth->user();

        return $user;
    }

    public function add($idUser)
    {
        $validator = $this->validator(['id' => $idUser], [
            'id' => ['required', 'numeric']
        ]);
        if ($validator->fails()) {
            throw new StoreResourceFailedException("Validation Error",
                $validator->errors());
        }

        if ($idUser == $this->auth->user()->id) {
            return response([
                'message' => trans('messages.friends.by_myself')
            ],422);
        }

        $user = User::find($idUser);

        if (!$user) {
            return response([
                'message' => trans('messages.friends.not_found')
            ])->status(404);
        }

        $friend = Friend::query()
            ->where('id_friend_one', '=', $idUser)
            ->orWhere('id_friend_two', '=', $idUser)
            ->first();
        if ($friend) {
            return response([
                'message' => trans('messages.friends.already_exists', ['name' => $user->name])
            ], 422);
        }

        $friend = new Friend();
        $friend->id_friend_one = $this->auth->user()->id;
        $friend->id_friend_two = $user->id;
        if ($friend->save()) {
            return response([
                'message' => trans('messages.friends.add_success', ['name' => $user->name])
            ], 201);
        }

    }

    protected function validator(array $data, array $rules)
    {
        return Validator::make($data, $rules);
    }
}