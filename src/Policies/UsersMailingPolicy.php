<?php

namespace FastDog\User\Policies;

use FastDog\User\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class UsersMailingPolicy
 *
 * @package FastDog\User\Policies
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UsersMailingPolicy
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

    /**
     * @param User $user
     * @param $ability
     * @return bool
     */
    public function reorder(User $user, $ability): bool
    {
        return ($user->{User::TYPE} === User::USER_TYPE_ADMIN);
    }

    /**
     * @param User $user
     * @param $ability
     * @return bool
     */
    public function create(User $user, $ability): bool
    {
        return ($user->{User::TYPE} === User::USER_TYPE_ADMIN);
    }

    /**
     * @param User $user
     * @param $ability
     * @return bool
     */
    public function delete(User $user, $ability): bool
    {
        return ($user->{User::TYPE} === User::USER_TYPE_ADMIN);
    }

    /**
     * @param User $user
     * @param $ability
     * @return bool
     */
    public function update(User $user, $ability): bool
    {
        return ($user->{User::TYPE} === User::USER_TYPE_ADMIN);

    }

    /**
     * @param User $user
     * @param $ability
     * @return bool
     */
    public function before(User $user, $ability): bool
    {
        return ($user->{User::TYPE} === User::USER_TYPE_ADMIN);
    }
}
