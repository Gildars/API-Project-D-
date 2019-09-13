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

    public function add(int $idUser)
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
            ], 422);
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
                'message' => trans('messages.friends.add_success',
                    [
                        'id' => $user->id,
                        'name' => $user->name
                    ]
                )
            ], 201);
        }

    }

    public function getFriends()
    {
        $friends = User::query()
            ->rightJoin('friend',
                function ($join) {
                    $join->on('friend.id_friend_one', '=', 'user.id')
                        ->orOn('friend.id_friend_two', '=', 'user.id');
                })
            ->where('user.id', '!=', $this->auth->user()->id)
            ->orWhere(function ($query) {
                $query->where('user.id', '=', 'friend.id_friend_one')
                    ->orWhere('user.id', '=', 'friend.id_friend_two');
            })
            ->orderBy('isOnline', 'DESC')
            ->get(['user.id', 'user.name', 'user.last_activity AS isOnline']);

        foreach ($friends as $friend) {
            $friend->isOnline = $friend->isOnline('isOnline');
        }

        if ($friends) {
            return response($friends);
        } else {
            return response([
                'message' => trans('messages.friends.friends_not_found')
            ]);
        }
    }

    public function deleteFriend(int $idFriend)
    {
        $friend = Friend::query()
            ->rightJoin('user',
                function ($join) {
                    $join->on('friend.id_friend_one', '=', 'user.id')
                        ->orOn('friend.id_friend_two', '=', 'user.id');
                })
            ->where(function ($query) use ($idFriend) {
                $query->where('friend.id_friend_one', '=', $idFriend)
                    ->where('friend.id_friend_two', '=', $this->auth->user()->id);
            })
            ->orWhere(function ($query) use ($idFriend) {
                $query->where('friend.id_friend_one', '=', $this->auth->user()->id)
                    ->where('friend.id_friend_two', '=', $idFriend);
            })
            ->first(['friend.id AS id', 'user.name']);

        if ($friend && $friend->delete()) {
            return response([
                'messages' => trans('messages.friends.deleted', ['name' => $friend->name])
            ], 200);
        }

        return response([
            'messages' => trans('messages.friends.not_found')
        ], 404);
    }

    protected function validator(
        array $data,
        array $rules
    ) {
        return Validator::make($data, $rules);
    }

}