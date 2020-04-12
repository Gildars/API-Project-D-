<?php

namespace App\Http\Api\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Conversation\DeleteConversationRequest;
use App\Repositories\ConversationsRepository;
use Dingo\Api\Routing\Helpers;
use Nahid\Talk\Facades\Talk;
use Auth;
/**
 * Class ThreadsController
 * @package App\Http\Api\Controllers
 */
class ThreadsController extends BaseController
{
    use Helpers;


    protected $friendRepository;

    /**
     * ThreadsController constructor.
     * @param ConversationsRepository $conversationsRepository
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
     * @OA\Delete(
     *     path="/threads",
     *     description="Удаляет диалог.",
     *     tags={"threads"},
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     * @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     * ),
     * @OA\Response(response="200", description="Диалог успешно удален."),
     * @OA\Response(response="404", description="Диалог не найден."),
     * )
     */
    /**
     * TODO Написать тест
     * @param int $skip
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteConversation(DeleteConversationRequest $request)
    {
        $conversations = $this->conversationsRepository->deleteConversations($request->id);
        if (!$conversations) {
            return response()->json(["message" => trans('messages.conversations.not_found'
            )], 404);
        }

        return response()->json($conversations, 200);
    }

    /**
     * @OA\Get(
     *     path="/threads",
     *     description="Возвращает список диалогов.",
     *     tags={"threads"},
     * @OA\Parameter(
     *     name="skip",
     *     in="path",
     *     required=false,
     * @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     * ),
     * @OA\Response(response="200", description="Список диалогов."),
     * @OA\Response(response="404", description="Диалоги не найдены."),
     * )
     */
    /**
     * TODO Написать тест
     * @param int $skip
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInbox(int $skip = 0)
    {
        $conversations = $this->conversationsRepository->getConversationsAll($skip);
        if ($conversations->isEmpty()) {
            return response()->json(["message" => trans('messages.conversations.not_found')], 404);
        }

        return response()->json($conversations, 200);
    }

}
