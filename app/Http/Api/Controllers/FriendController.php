<?php

namespace App\Http\Api\Controllers;

use App\Character;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Friend\StoreFriendRequest;
use App\Repositories\FriendRepository;
use App\Repositories\UserRepository;
use Dingo\Api\Routing\Helpers;
use Illuminate\Support\Facades\Auth;

class FriendController extends BaseController
{
    use Helpers;


    protected $friendRepository;

    public function __construct(FriendRepository $friendRepository)
    {
        parent::__construct();
        $this->middleware('api.auth');
        $this->friendRepository = $friendRepository;
    }

    /**
     * @OA\Post(
     *     path="/friends/{id}",
     *     description="Добавляет игрока в друзья.",
     *     tags={"friends"},
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     * @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     * ),
     * @OA\Response(response="201", description="Игрок успешно добавлен в друзья."),
     * @OA\Response(response="422", description="Нельзя добавить самого себя в друзья."),
     * @OA\Response(response="404", description="Игрок не найден."),
     *
     * )
     * @param UserRepository $userRepository
     * @param StoreFriendRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function add(UserRepository $userRepository, StoreFriendRequest $request, string $name)
    {
        if ($this->friendRepository->getCountFriends($this->auth->user()->character->id) >= config('game.communicator.max_friends')) {
            return response(
                [
                    'message' =>  trans('messages.friends.max_friends')
                ], 422
            );
        }

        if ($request->name == $this->auth->user()->character->name) {
            return response(
                [
                'message' => trans('messages.friends.by_myself')
                ], 422
            );
        }

        $character = Character::where('name',$name)->first();

        if (!$character) {
            return response(
                [
                'message' => trans('messages.friends.not_found')
                ], 404
            );
        }

        if ($this->friendRepository->getById($character->id)) {
            return response(
                [
                'message' => trans('messages.friends.already_exists', ['name' => $character->name])
                ], 422
            );
        }

        if ($this->friendRepository->create($this->auth->user()->character->id, $character->id)) {
            return response(
                [
                'message' => trans(
                    'messages.friends.add_success',
                    [
                        'id' => $character->id,
                        'name' => $character->name
                    ]
                )
                ], 201
            );
        }

    }

    /**
     * @OA\Get(
     *     path="/friends",
     *     description="Получить список друзей.",
     *     tags={"friends"},
     * @OA\Response(response="200", description="Список друзей."),
     * @OA\Response(response="404", description="Список друзей пуст."),
     * )
     */
    public function getFriends($skip = 0)
    {
        $friends = $this->friendRepository->getFriends($this->auth->user()->character->id, $skip);
        if ($friends) {
            return response()->json($friends, 200);
        }
        return response(
            [
            'message' => trans('messages.friends.friends_not_found')
            ], 404
        );
    }

    /**
     * @OA\Delete(
     *     path="/friends/{id}",
     *     description="Удаляет игрока из списка друзей.",
     *     tags={"friends"},
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     * @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     * ),
     * @OA\Response(response="200", description="Игрок успешно удален из списка друзей."),
     * @OA\Response(response="404", description="Игрок не найден."),
     * )
     */
    public function deleteFriend(int $friendId)
    {
        $friend = $this->friendRepository->getFriend(Auth::id(), $friendId);
        if ($friend && $this->friendRepository->deleteFriend($friend)) {
            return response(
                [
                'message' => trans('messages.friends.deleted', ['name' => $friend->name])
                ], 200
            );
        }

        return response(
            [
            'message' => trans('messages.friends.not_found')
            ], 404
        );
    }

}
