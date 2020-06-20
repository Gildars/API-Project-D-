<?php

namespace App\Repositories;

use App\Character;
use App\Friend;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class FriendRepository
 *
 * @package App\Repositories
 */
class FriendRepository extends BaseRepository
{
    /**
     * @var Friend
     */
    protected $model;

    /**
     * @var User
     */
    protected $user;

    /**
     * FriendRepository constructor.
     *
     * @param Friend $friend
     * @param User $user
     */
    public function __construct(Friend $friend, User $user)
    {
        $this->model = $friend;
        $this->user = $user;
    }

    /**
     * @param int $id
     * @return \App\Models\Model
     */
    public function getById($id)
    {
        $authId = Auth::id();
        return $this->model
            ->where(
                function ($query) use ($id, $authId) {
                    $query->where('friends.id_friend_one', '=', $id)
                        ->where('friends.id_friend_two', '=', $authId);
                }
            )
            ->orWhere(
                function ($query) use ($id, $authId) {
                    $query->where('friends.id_friend_one', '=', $authId)
                        ->where('friends.id_friend_two', '=', $id);
                }
            )
            ->first();
    }

    /**
     * @param  $userId
     * @param  $friendId
     * @return mixed
     */
    public function create(string $userId, string $friendId)
    {
        $friend = new $this->model;
        $friend->id = Str::uuid();
        $friend->id_friend_one = $userId;
        $friend->id_friend_two = $friendId;
        $friend->timestamps = false;
        $friend->save();
        return $friend;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getFriends(string $id, int $skip)
    {
        $characters = Character::rightJoin(
            'friends',
            function ($join) {
                $join->on('friends.id_friend_one', '=', 'characters.id')
                    ->orOn('friends.id_friend_two', '=', 'characters.id');
            }
        )
            ->where(
                function ($query) use ($id) {
                    $query->where('friends.id_friend_one', '=', $id)
                        ->whereRaw('friends.id_friend_two = characters.id');
                }
            )
            ->orWhere(
                function ($query) use ($id) {
                    $query->whereRaw('friends.id_friend_one = characters.id')
                        ->where('friends.id_friend_two', '=', $id);
                }
            )
            ->with(['location:id,name','characterClass:id,name','user'])
            ->skip($skip)
            ->take(15)
            ->get([DB::raw('true as online'),'characters.id','characters.name','characters.gender','characters.location_id','characters.character_class_id','characters.level_id','characters.user_id']);
        if (!$characters->isEmpty()) {
            $characters = collect($characters)->each(function ($character) {
                 $character->online = $character->user->isOnline();
            });
        }
        return $characters;
    }


    /**
     * TODO добавить роут для этой функции
     * @param int $id
     * @return mixed
     */
    public function getCountFriends(string $id)
    {
        $friends = $this->model->where('friends.id_friend_one', '=', $id)
            ->where('friends.id_friend_one', '=', $id)->get(['id']);
        return $friends->count();
    }

    /**
     * @param int $userId
     * @param int $friendId
     * @return mixed
     */
    public function getFriend(string $userId, string $friendId)
    {
        $friend = $this->model->rightJoin(
            'users',
            function ($join) {
                $join->on('friends.id_friend_one', '=', 'users.id')
                    ->orOn('friends.id_friend_two', '=', 'users.id');
            }
        )
            ->where(
                function ($query) use ($friendId, $userId) {
                    $query->where('friends.id_friend_one', '=', $friendId)
                        ->where('friends.id_friend_two', '=', $userId);
                }
            )
            ->orWhere(
                function ($query) use ($friendId, $userId) {
                    $query->where('friends.id_friend_one', '=', $userId)
                        ->where('friends.id_friend_two', '=', $friendId);
                }
            )
            ->first(['friends.id AS id', 'users.name']);

        return $friend;
    }

    /**
     * @param Friend $friend
     * @return bool|null
     * @throws \Exception
     */
    public function deleteFriend(Friend $friend)
    {
        $result = $friend->delete();
        return $result;
    }
}
