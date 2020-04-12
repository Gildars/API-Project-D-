<?php

namespace App\Repositories;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Nahid\Talk\Conversations\Conversation;
use Nahid\Talk\Facades\Talk;

/**
 * Class ConversationsRepository
 * @package App\Repositories
 */
class ConversationsRepository extends BaseRepository
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
     * @param int $skip
     * @return mixed
     */
    public function getConversationsAll(int $skip)
    {
        $conversations = Talk::getInbox('desc', $offset = $skip, $take = 15);
        foreach ($conversations as $thread) {
            $thread->withUser->last_activity = $thread->withUser->isOnline;
        }
        return $conversations;
    }

    public function deleteConversations(int $idConversation)
    {
        return Talk::deleteConversations($idConversation);
    }

    public function getConversationsByUserId($senderId, $offset = 0, $take = 20)
    {
        $conversationId = Talk::isConversationExists($senderId, Auth::id());
        if ($conversationId) {
            return $this->getConversationsById($conversationId, $senderId, $offset, $take);
        }

        return false;
    }

    public function getConversationsById($conversationId, $userId, $skip, $take)
    {


        $conversation = Conversation::with(
            [
                'messages' => function ($query) use ($userId, $skip, $take) {
                    $query->where(
                        function ($qr) use ($userId) {
                            $qr->where('user_id', '=', $userId)
                                ->where('deleted_from_sender', 0);
                        }
                    )
                        ->orWhere(
                            function ($q) use ($userId) {
                                $q->where('user_id', '!=', $userId)
                                    ->where('deleted_from_receiver', 0);
                            }
                        );

                    $query->orderBy('created_at','desc')->skip($skip)->take($take);

                }
            ]
        )->with(['userone', 'usertwo'])->find($conversationId);
        if (!$conversation->messages->count()) {
            return false;
        }
        return $this->makeMessageCollection($conversation);
    }

    protected function makeMessageCollection($conversations)
    {
        if (!$conversations) {
            return false;
        }

        $collection = (object)null;
        if ($conversations->user_one == Auth::id() || $conversations->user_two == Auth::id()) {
            $withUser = ($conversations->userone->id === Auth::id()) ? $conversations->usertwo : $conversations->userone;
            $collection->withUser = $withUser;
            $collection->messages = $conversations->messages;

            return $collection;
        }

        return false;
    }

}
