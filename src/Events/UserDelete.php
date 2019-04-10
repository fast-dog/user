<?php

namespace FastDog\User\Events;

use FastDog\User\User;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

/**
 * Обновление профиля
 *
 * @package FastDog\User\Events
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserDelete
{
    use  SerializesModels;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Request
     */
    protected $request;

    /**
     * UserUpdate constructor.
     * @param User $user
     * @param Request $request
     */
    public function __construct(User &$user, Request $request)
    {
        $this->user = &$user;
        $this->setRequest($request);
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
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
