<?php

namespace FastDog\User\Events;

use FastDog\User\Models\User;
use Illuminate\Queue\SerializesModels;

/**
 * Регистрация
 *
 * @package FastDog\User\Events
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserRegistration
{
    use  SerializesModels;


    /**
     * @var User
     */
    protected $user;

    /**
     * UserRegistration constructor.
     * @param User $user
     */
    public function __construct(User &$user)
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
