<?php

namespace App\Http\Api\Controllers;

use App\Events\ChatMessage;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Conversation\StoreConversationMessageRequest;
use App\Models\User;
use App\Repositories\ConversationsRepository;
use Illuminate\Http\Request;
use Nahid\Talk\Facades\Talk;
use Auth;
use Dingo\Api\Routing\Helpers;

/**
 * Class MessageController
 *
 * @package App\Http\Api\Controllers
 */
class MessageController extends BaseController
{
    use Helpers;
    /**
     * @var
     */
    protected $authUser;

    protected $conversationsRepository;

    /**
     * MessageController constructor.
     */
    public function __construct(ConversationsRepository $conversationsRepository)
    {
        parent::__construct();
        $this->middleware(
            function ($request, $next) {
                Talk::setAuthUserId(Auth::user()->id);
                return $next($request);
            }
        );
        $this->conversationsRepository = $conversationsRepository;
    }

    /**
     * @OA\Get(
     *     path="/messages/{id}/{offset}",
     *     description="Возвращает историю переписки с другим пользователем.",
     *     tags={"messages"},
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     * @OA\Schema(
     *           type="integer",
     *           format="int32"
     *         )
     * ),
     * @OA\Parameter(
     *     name="offset",
     *     in="path",
     *     required=true,
     * @OA\Schema(
     *           type="integer",
     *           format="int32"
     *         )
     * ),
     * @OA\Response(response="200", description="Возвращает список сообщений."),
     * @OA\Response(response="404", description="Сообщения не найдены."),
     * )
     */
    public function chatHistory($id, int $skip = 0)
    {
        $conversations = $this->conversationsRepository->getConversationsByUserId($id,$skip,6);

        if (!$conversations) {
            return response()->json(
                [
                    [
                        'message' => trans('messages.message.not_found_messages')
                    ]
                ],
                404
            );
        }
/*
        if (count($conversations->messages) > 0) {
            $conversations->messages = $conversations->messages->sortBy('created_at');
            $conversations->messages->reverse()->all();
        }*/
        return response()->json($conversations->messages, 200);
    }

    /**
     * @OA\Post(
     *     path="/messages/{id}",
     *     description="Отправляет сообщение игроку.",
     *     tags={"messages"},
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     * @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     * ),
     * @OA\Parameter(
     *     name="message",
     *     in="query",
     *     required=true,
     * @OA\Schema(
     *             type="string",
     *         )
     * ),
     * @OA\Parameter(
     *     name="message",
     *     in="query",
     *     required=true,
     * @OA\Schema(
     *             type="string",
     *         )
     * ),
     * @OA\Parameter(
     *     name="offset",
     *     in="query",
     *     required=true,
     * @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     * ),
     * @OA\Response(response="201", description="Сообщение отправленно."),
     * @OA\Response(response="404", description="Пользователь не найден."),
     * @OA\Response(response="422", description="Не удалось отправить сообщение."),
     * )
     * @param                       StoreConversationMessageRequest $request
     * @return                      \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function sendMessage(StoreConversationMessageRequest $request)
    {
        if (!User::find($request->id)) {
            return response(
                [
                    'message' => trans('messages.user.not_found')
                ],
                404
            );
        }
        if ($message = Talk::sendMessageByUserId($request->id, $request->message)) {
            broadcast(new ChatMessage($message))->toOthers();
            return response()->json($message,
                201);
        }
        return response(
            [
                'message' => trans('messages.message.not_fount')
            ],
            422
        );
    }

    /**
     * @OA\Delete(
     *     path="/messages/{id}",
     *     description="Удаляет сообщение.",
     *     tags={"messages"},
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
     * @OA\Response(response="422", description="Не удалось удалить сообщение."),
     * )
     * @param                       Request $request
     * @param                       $id
     * @return                      \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function deleteMessage(Request $request, $id)
    {
        if (Talk::deleteMessage($id)) {
            return response(
                [
                    'message' => trans('messages.message.delete')
                ],
                200
            );
        }
        return response()->json(
            [
                'message' => trans('messages.message.not_fount')
            ],
            404
        );
    }


    /**
     *
     */
    public function tests()
    {
        dd(Talk::channel());
    }
}
