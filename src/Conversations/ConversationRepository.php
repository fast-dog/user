<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 07.06.2017
 * Time: 9:31
 */

namespace FastDog\User\Conversations;


use Nahid\Talk\Conversations\Conversation;
use Nahid\Talk\Conversations\ConversationRepository as TalkConversationRepository;

/**
 * Class ConversationRepository
 * @package FastDog\User\Conversations
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class ConversationRepository extends TalkConversationRepository
{

    /*
 * get all conversations by given conversation id
 *
 * @param   int $conversationId
 * @param   int $userId
 * @param   int $offset
 * @param   int $take
 * @return  collection
 * */
    public function getMessagesById($conversationId, $userId, $offset, $take)
    {

        return Conversation::with(['messages' => function ($query) use ($userId, $offset, $take, $conversationId) {
            $query->where(function ($qr) use ($userId, $conversationId) {
                $qr->where('user_id', '=', $userId)
                    ->where('conversation_id', $conversationId)
                    ->where('deleted_from_sender', 0);
            })
                ->orWhere(function ($q) use ($userId, $conversationId) {
                    $q->where('user_id', '!=', $userId)
                        ->where('conversation_id', $conversationId)
                        ->where('deleted_from_receiver', 0);
                });

            $query->offset($offset)->take($take);
            //$query->orderBy('created_at', 'desc');

        }])
            ->with(['userone', 'usertwo'])
            ->find($conversationId);

    }
}