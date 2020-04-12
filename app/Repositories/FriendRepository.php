<?php

namespace App\Repositories;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

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
     * @param  int $id
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
    public function create($userId, $friendId)
    {
        $friend = new $this->model;
        $friend->id_friend_one = $userId;
        $friend->id_friend_two = $friendId;
        $friend->save();
        return $friend;
    }

    /**
     * @param  int $id
     * @return mixed
     */
    public function getFriends(int $id, int $skip)
    {
        $friends = $this->user->rightJoin(
            'friends',
            function ($join) {
                $join->on('friends.id_friend_one', '=', 'users.id')
                    ->orOn('friends.id_friend_two', '=', 'users.id');
            }
        )
            ->where(
                function ($query) use ($id) {
                    $query->where('friends.id_friend_one', '=', $id)
                        ->whereRaw('friends.id_friend_two = users.id');
                }
            )
            ->orWhere(
                function ($query) use ($id) {
                    $query->whereRaw('friends.id_friend_one = users.id')
                        ->where('friends.id_friend_two', '=', $id);
                }
            )
            ->orderBy('isOnline', 'DESC')
            ->skip($skip)
            ->take(15)
            ->get(['users.id', 'users.lvl', 'users.name', 'users.last_activity AS isOnline']);
        if (!$friends->isEmpty()) {
            foreach ($friends as $friend) {
                $friend->last_activity = $friend->isOnline;
            }
            return $friends;
        } else {
            return false;
        }
    }


    /**
     * TODO добавить роут для этой функции
     * @param int $id
     * @return mixed
     */
    public function getCountFriends(int $id){
        $friends = $this->model->where('friends.id_friend_one', '=', $id)
            ->where('friends.id_friend_one', '=', $id)->get(['id']);
        return $friends->count();
    }

    /**
     * @param  int $userId
     * @param  int $friendId
     * @return mixed
     */
    public function getFriend(int $userId, int $friendId)
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
     * @param  Friend $friend
     * @return bool|null
     * @throws \Exception
     */
    public function deleteFriend(Friend $friend)
    {
        $result = $friend->delete();
        return $result;
    }
}
