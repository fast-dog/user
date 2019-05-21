<?php

namespace App\Policies;

use FastDog\User\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class UserPolicy
 * 
 * @package App\Policies
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function before(User $user, $ability)
    {
        if ($user->{User::TYPE} === User::USER_TYPE_ADMIN) {
            return true;
        }
    }
}
