<?php

namespace App\Http\Api\Controllers;

use App\Events\ChatMessage;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Converstion\StoreConversationMessageRequests;
use App\Http\Requests\StoreConversationMessage;
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

    /**
     * MessageController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('api.auth');
        $this->middleware(
            function ($request, $next) {
                Talk::setAuthUserId(Auth::user()->id);
                return $next($request);
            }
        );
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
    public function chatHistory($id, $offset = 0)
    {
        $conversations = Talk::getMessagesByUserId($id, $offset, 10);
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

        if (count($conversations->messages) > 0) {
            $conversations->messages = $conversations->messages->sortBy('id');
        }
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
     * @OA\Response(response="422", description="Не удалось отправить сообщение."),
     * )
     * @param                       StoreConversationMessageRequests $request
     * @return                      \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function sendMessage(StoreConversationMessageRequests $request)
    {
        if ($message = Talk::sendMessageByUserId($request->id, $request->message)) {
            broadcast(new ChatMessage($message))->toOthers();
            return response(
                [
                    'message' => trans('messages.message.send')
                ],
                201
            );
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
    public function getInbox()
    {
        if ($inboxes = Talk::getInbox()) {
            return response()->json(
                [
                $inboxes
                ]
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
