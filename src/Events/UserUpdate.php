<?php

namespace FastDog\User\Events;


use FastDog\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

/**
 * Обновление профиля
 *
 * @package FastDog\User\Events
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserUpdate
{
    use  SerializesModels;

    /**
     * @var User
     */
    protected $user;

    /**
     * UserUpdate constructor.
     * @param User $user
     * @param Request $request
     */
    public function __construct(User &$user, Request $request)
    {
        $this->user = &$user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
