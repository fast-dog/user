<?php

namespace FastDog\User\Events;

use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Nahid\Talk\Messages\Message;

/**
 * Добавление сообщения в чат
 *
 * @package FastDog\User\Events
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class AddChatMessage
{
    use  SerializesModels;

    /**
     * @var Message
     */
    protected $message;

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }



    /**
     * @var Request
     */
    protected $request;

    /**
     * UserUpdate constructor.
     * @param Message $message
     * @param Request $request
     */
    public function __construct(Message &$message, Request $request)
    {
        $this->message = &$message;
        $this->setRequest($request);
    }


    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }


}
