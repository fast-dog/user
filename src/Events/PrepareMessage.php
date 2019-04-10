<?php

namespace FastDog\User\Events;

use Illuminate\Queue\SerializesModels;
use Nahid\Talk\Messages\Message;

/**
 * Информация о сообщение чата
 *
 * Событие вызывается при обрбаотке сообщений чата
 *
 * @package FastDog\User\Events
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class PrepareMessage
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
     * @var array $data
     */
    protected $data;

    /**
     * UserUpdate constructor.
     * @param Message $message
     * @param array $data
     */
    public function __construct(Message &$message, &$data)
    {
        $this->message = &$message;
        $this->data = &$data;
    }


    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
