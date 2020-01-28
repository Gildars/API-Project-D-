<?php

namespace App\Http\Api\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\Friend;
use App\Models\User;
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
     */
    public function add(UserRepository $userRepository, int $idUser)
    {
        if ($idUser == $this->auth->user()->id) {
            return response(
                [
                'message' => trans('messages.friends.by_myself')
                ], 422
            );
        }

        $user = $userRepository->getById($idUser);

        if (!$user) {
            return response(
                [
                'message' => trans('messages.friends.not_found')
                ], 404
            );
        }

        if ($this->friendRepository->getById($idUser)) {
            return response(
                [
                'message' => trans('messages.friends.already_exists', ['name' => $user->name])
                ], 422
            );
        }

        if ($this->friendRepository->create(Auth::id(), $idUser)) {
            return response(
                [
                'message' => trans(
                    'messages.friends.add_success',
                    [
                        'id' => $user->id,
                        'name' => $user->name
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
    public function getFriends()
    {
        $friends = $this->friendRepository->getFriends(Auth::id());
        if ($friends) {
            return response($friends, 200);
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
                'messages' => trans('messages.friends.deleted', ['name' => $friend->name])
                ], 200
            );
        }

        return response(
            [
            'messages' => trans('messages.friends.not_found')
            ], 404
        );
    }

}
