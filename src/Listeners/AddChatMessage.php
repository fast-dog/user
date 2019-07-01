<?php

namespace FastDog\User\Listeners;

use FastDog\Media\Models\GalleryItem;
use FastDog\User\User;
use Illuminate\Http\Request;
use Nahid\Talk\Messages\Message;


/**
 * Добавление сообщения в чат
 *
 * Событие вызывается после добавления сообщения
 *
 * @package FastDog\User\Listeners
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class AddChatMessage
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * UpdateProfile constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Обработчик
     *
     * @param \FastDog\User\Events\AddChatMessage $event
     * @return void
     */
    public function handle(\FastDog\User\Events\AddChatMessage $event)
    {
        /**
         * @var $user User
         */
        $user = \Auth::getUser();
        /**
         * @var $message Message
         */
        $message = $event->getMessage();
        $count = GalleryItem::where([
            GalleryItem::USER_ID => $user->id,
            GalleryItem::PARENT_TYPE => GalleryItem::TYPE_CHAT_MESSAGE,
            GalleryItem::PARENT_ID => 0,
        ])->count();

        if ($count > 0) {
            GalleryItem::where([
                GalleryItem::USER_ID => $user->id,
                GalleryItem::PARENT_TYPE => GalleryItem::TYPE_CHAT_MESSAGE,
                GalleryItem::PARENT_ID => 0,
            ])->update([
                GalleryItem::PARENT_ID => $message->id,
            ]);
        }
    }
}
