<?php

namespace FastDog\User\Events;


 
use FastDog\User\Models\User;
use Illuminate\Queue\SerializesModels;

/**
 * Получение данных пользователя
 *
 * @package FastDog\User\Events
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class GetUserData
{
    use  SerializesModels;

    /**
     * @var array $data
     */
    protected $data = [];

    /**
     * @var User $user
     */
    protected $user;

    /**
     * GetUserData constructor.
     * @param $data
     * @param User $user
     */
    public function __construct(&$data, User &$user)
    {
        $this->data = &$data;
        $this->user = &$user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
