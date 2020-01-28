<?php

namespace App\Repositories;

use App\Models\Friend;
use App\Models\User;

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
     * @param User   $user
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
        return $this->model
            ->where('id_friend_one', '=', $id)
            ->orWhere('id_friend_two', '=', $id)
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
    public function getFriends(int $id)
    {
        $friends = $this->user
            ->join(
                'friends',
                function ($join) {
                    $join->on('friends.id_friend_one', '=', 'users.id')
                        ->orOn('friends.id_friend_two', '=', 'users.id');
                }
            )
            ->where('users.id', '!=', $id)
            ->orWhere(
                function ($query) {
                    $query->where('users.id', '=', 'friends.id_friend_one')
                        ->orWhere('users.id', '=', 'friends.id_friend_two');
                }
            )
            ->orderBy('isOnline', 'DESC')
            ->get(['users.id', 'users.name', 'users.last_activity AS isOnline']);

        foreach ($friends as $friend) {
            $friend->isOnline = $friend->isOnline('isOnline');
        }

        return $friends;
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
