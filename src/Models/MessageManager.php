<?php

namespace FastDog\User\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Nahid\Talk\Conversations\Conversation;
use Nahid\Talk\Messages\Message;
use stdClass;


/**
 * Поддержка обмена сообщениями между пользователями
 *
 * @package FastDog\User\Models
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 * @deprecated
 */
class MessageManager
{
    /**
     * Идентификатор текущего пользователя
     *
     * @var $userId integer
     */
    protected $userId;

    /**
     * MessageManager constructor.
     */
    public function __construct()
    {
        $user = auth()->user();
        if ($user) {
            $this->auth($user->id);
        } else {
            $this->auth(0);
        }
    }

    /**
     * Авторизация пользователя
     *
     * @param $userId
     */
    public function auth($userId)
    {
        $this->userId = $userId;
       // \Talk::setAuthUserId($this->userId);
    }

    /**
     * Список входящих
     *
     * @param string $order
     * @param int $offset
     * @param int $take
     * @return mixed
     */
    public function getInbox($order = 'desc', $offset = 0, $take = 20)
    {
        return \Talk::getInbox($order, $offset, $take);
    }

    /**
     * Возвращает не прочитанные сообщения
     *
     * @return null|LengthAwarePaginator|Collection
     */
    public function getUnreadMessages()
    {
        if ($this->unreadMessages === null) {
            $this->getUnreadCount();
        }

        return collect([]);// $this->unreadMessages;
    }

    /**
     * Отправка сообщения
     *
     * @param $userId integer
     * @param $message string
     * @return mixed
     */
    public function sendMessageByUserId($userId, $message)
    {
        return \Talk::sendMessageByUserId($userId, $message);
    }

    /**
     * @param $receiverId
     * @param int $offset
     * @param int $take
     * @return mixed
     */
    public function getMessagesByUserId($receiverId, $offset = 0, $take = 20)
    {
        return \Talk::getMessagesByUserId($receiverId, $offset, $take);
    }

    public $conversationIds = [];
    public $unreadMessages = null;

    /**
     * Кол-во не прочитанных сообщений
     *
     * @return int
     */
    public function getUnreadCount()
    {

//        if ($this->conversationIds == []) {
//            /**
//             * @var $conversations Collection
//             */
//            $conversations = Conversation::where(function ($query) {
//                $query->whereRaw(\DB::raw("(user_two = {$this->userId} OR user_one = {$this->userId})"));
//            })->get(['id']);
//
//            $conversations->each(function ($item) {
//                array_push($this->conversationIds, $item->id);
//            });
//        }
//
//        if (count($this->conversationIds) && $this->unreadMessages === null) {
//            $this->unreadMessages = Message::where(function ($query) {
//                $query->where('is_seen', 0);
//                $query->where('user_id', '!=', $this->userId);
//                $query->whereIn('conversation_id', $this->conversationIds);
//            })->paginate(25);
//
//            return $this->unreadMessages->total();
//        } elseif ($this->unreadMessages) {
//            return $this->unreadMessages->total();
//        }

        return 0;
    }

    /**
     * @param $conversation_id
     * @return mixed
     */
    public function getUnreadCountInConversationId($conversation_id)
    {
//        $result = ViewNewMessages::where(function ($query) use ($conversation_id) {
//            $query->where(function ($query) {
//                $query->where('user_two', $this->userId);
//                $query->whereOr('user_one', $this->userId);
//            });
//            $query->where('conversation_id', $conversation_id);
//            $query->where('sender_id', '!=', $this->userId);
//        })->count();
        $result = Message::where(function (Builder $query) use ($conversation_id) {
            $query->where('is_seen', 0);
            $query->where('user_id', '!=', $this->userId);
            $query->where('conversation_id', $conversation_id);
        })->count();


        return $result;
    }

    /**
     * Кол-во не прочтенных сообщений
     *
     * @param $receiverId integer
     * @return int
     */
    public function getUnreadCountByUserId($receiverId)
    {
        $result = \DB::select(<<<SQL
SELECT count(*) as agregate  FROM conversations c LEFT JOIN
messages m ON m.conversation_id = c.id AND m.is_seen = 0
WHERE c.user_one = '{$this->userId}' AND c.user_two = '{$receiverId}';
SQL
        );
        $result = Arr::first($result);

        return (isset($result->{'agregate'})) ? $result->{'agregate'} : 0;
    }

    /**
     * Кол-во сообщений пользователю
     *
     * @param $receiverId integer
     * @return int
     */
    public function getCountByUserId($receiverId)
    {
        $result = \DB::select(<<<SQL
SELECT count(*) as agregate  FROM conversations c LEFT JOIN
messages m ON m.conversation_id = c.id
WHERE c.user_one = '{$this->userId}' AND c.user_two = '{$receiverId}';
SQL
        );
        $result = Arr::first($result);

        return (isset($result->{'agregate'})) ? $result->{'agregate'} : 0;
    }

    /**
     * Короткий список последних сообщений
     *
     * @return array
     */
    public function getNew()
    {
        $result = [
            'total' => $this->getUnreadCount(),
            'items' => [],
        ];
        $inbox = $this->getInbox('desc', 0, 5);
        Carbon::setLocale('ru');
        foreach ($inbox as $message) {
            if ($message->thread) {
                array_push($result['items'], [
                    'id' => $message->thread->id,
                    'message' => Str::limit($message->thread->message, 150),
                    'created_at' => $message->thread->created_at->format('d.m.y H:i'),
                    'created_at_diff' => $message->thread->humans_time,
                    'photo' => $message->thread->sender->getPhoto(),
                ]);
            }
        }

        return $result;
    }

    /**
     * Кол-во страниц в диалоге
     *
     * @var int $pages
     */
    protected $pages = 0;
    protected $count = 0;

    /**
     * @return int
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param int $offset
     * @param int $take
     * @param int $page
     * @return int
     */
    public function getOffset($offset = 0, $take = 20, $page = 1)
    {
        if ($this->count > $take) {
            $offset = $this->count - ($take * $page);
        }

        return $offset;
    }

    /**
     * Получение чата
     *
     * @param $conversationId
     * @param int $offset
     * @param int $take
     * @param int $page
     * @return mixed
     */
    public function getConversationsById($conversationId, $offset = 0, $take = 5, $page = 1)
    {
        $userId = $this->userId;
        $this->count = Message::where(function (Builder $query) use ($userId, $conversationId) {
            $query->where(function (Builder $qr) use ($userId, $conversationId) {
                $qr->where('user_id', '=', $userId)
                    ->where('conversation_id', $conversationId)
                    ->where('deleted_from_sender', 0);
            })->orWhere(function (Builder $q) use ($userId, $conversationId) {
                $q->where('user_id', '!=', $userId)
                    ->where('conversation_id', $conversationId)
                    ->where('deleted_from_receiver', 0);
            });
        })->count();


        $this->pages = (int)ceil($this->count / $take);
        $offset = $this->getOffset($offset, $take, $page);

        $conversation = \Talk::getConversationsById($conversationId, $offset, $take);


        return $conversation;
    }

    /**
     * Удаление чата
     *
     * @param $conversationId
     * @return mixed
     */
    public function deleteConversations($conversationId)
    {
        return \Talk::deleteConversations($conversationId);
    }

    /**
     * Отправка нового сообщения пользователю
     *
     * Метод создает новый чат
     * @param $receiverId
     * @param int $offset
     * @param int $take
     * @return mixed
     */
    public function getConversationsByUserId($receiverId, $offset = 0, $take = 20)
    {
        return \Talk::getConversationsByUserId($receiverId, $offset, $take);
    }

    /**
     * Возвращает идентификатор чата с пользователем
     *
     * @param $userId
     * @return mixed
     */
    public function isConversationExists($userId)
    {
        $item = Conversation::where(function (Builder $query) use ($userId) {
            $query->whereRaw("(user_one='{$this->userId}' AND user_two='{$userId}') OR (user_one='{$userId}' AND user_two='{$this->userId}')");
        })->first();
        if ($item) {
            return $item->id;
        }

        return \Talk::isConversationExists($userId);
    }

    /**
     * Отправка сообщения в чат
     *
     * @param $conversationId
     * @param $message
     * @return mixed
     */
    public function sendMessage($conversationId, $message)
    {
        return \Talk::sendMessage($conversationId, $message);
    }

    /**
     * Сообщение прочитано
     *
     * @param $messageId
     * @return mixed
     */
    public function makeSeen($messageId)
    {
        return \Talk::makeSeen($messageId);
    }

    /**
     * Список новых сообщений для информера
     *
     * Возвращает массив чатов и кол-во новых сообщений в них
     *
     * @return array
     */
    public function getInboxNew()
    {
        $result = [];

        if (null !== $this->unreadMessages) {
            foreach ($this->unreadMessages as $item) {
                $result[$item->conversation_id] = (object)[
                    'conversation' => $this->getConversationsById($item->conversation_id, 0, 1, 1),
                    'count' => ViewNewMessages::where(function (Builder $query) use ($item) {
                        $query->where('conversation_id', $item->conversation_id);
                    })->count(),
                ];
            }
        }

        return $result;
    }

    /**
     * @param $name
     * @param array $params
     * @return mixed
     */
    public function sendSystemMessages($name, array $params)
    {
        /**
         * Проверка настроек пользователя
         */
        if (isset($params['user'])) {
            if ($params['user']->setting->can(UserSettings::SEND_PERSONAL_MESSAGES)) {
                return false;
            }
        }

        /**
         * @var $tpl self
         */
        $tpl = Messages::where(function (Builder $query) use ($name) {
            $query->where(Messages::ALIAS, $name);
            $query->where(Messages::STATE, Messages::STATE_PUBLISHED);
        })->first();
        if ($tpl) {
            $text = null;

            if (is_string($name)) {
                if (isset($tpl->text)) {
                    $text = $tpl->text;
                }
            } else if ($name instanceof StdClass) {
                $text = $name->text;

            }
            $text = str_replace('&gt;', '>', $text);

            if ($text) {
                foreach ($params as $key => $value) {
                    $key = strtoupper($key);
                    if (!is_object($value)) {
                        $text = str_replace('{{' . $key . '}}', $value, $text);
                    } else {
                        if (method_exists($value, 'getArrayableAttributes')) {
//                            $attribs = $value->getArrayableAttributes();
//                            foreach ($attribs as $_key => $_value) {
//                                $text = str_replace('{{' . strtoupper($key) . '->' . strtoupper($_key) . '}}', $_value, $text);
//                            }
                        }
                    }
                }
                $params['content'] = $text;

                if (isset($params['user_id'])) {
                    return $this->sendMessageByUserId($params['user_id'], $params['content']);
                }
                if (isset($params['conversation_id'])) {
                    return $this->sendMessage($params['conversation_id'], $params['content']);
                }
            }
        }

        return false;
    }

    /**
     * @param $messageId
     * @return mixed
     */
    public function deleteMessage($messageId)
    {
        return \Talk::deleteMessage($messageId);
    }

    /**
     * Удаление сообщений в выбранном чате
     * @param $id
     */
    public function clearConversation($id)
    {
        $conversation = Conversation::where(function (Builder $query) use ($id) {
            $query->where('id', $id);
        })->first();

        if ($conversation) {
            $messages = Message::where([
                'conversation_id' => $conversation->id,
            ])->get();
            foreach ($messages as $message) {
                $this->deleteMessage($message->id);
            }
        }
    }

    /**
     * Получение списка файлов не прикрепленных к сообщениям
     *
     * @return array
     */
    public function getEmptyAttach()
    {
        $result = [];
        /**
         * @var $items Collection
         */
//        $items = GalleryItem::where([
//            GalleryItem::PARENT_TYPE => GalleryItem::TYPE_CHAT_MESSAGE,
//            GalleryItem::USER_ID => $this->userId,
//            GalleryItem::PARENT_ID => 0,
//        ])->get();
//
//        if ($items->isNotEmpty()) {
//            $items->each(function (GalleryItem $item, $idx) use (&$result) {
//                $thumb = Gallery::getPhotoThumb(str_replace([url('/')], '', $item->{GalleryItem::PATH}), 120);
//                array_push($result, [
//                    'id' => $item->id,
//                    'path' => $item->{GalleryItem::PATH},
//                    'thumbs' => $thumb,
//                ]);
//            });
//        }

        return $result;
    }
}
