<?php

namespace FastDog\User;


use FastDog\User\Policies\UserPolicy;
use FastDog\User\Policies\UsersMailingPolicy;
use FastDog\User\Policies\UsersMailingTemplatesPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * Class AuthServiceProvider
 *
 * @package FastDog\User
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Сопоставление политик для приложения.
     *
     * @var array
     */
    protected $policies = [
        \FastDog\User\Models\User::class => UserPolicy::class,
        \FastDog\User\Models\UserMailing::class => UsersMailingPolicy::class,
        \FastDog\User\Models\UserMailingTemplates::class => UsersMailingTemplatesPolicy::class,
    ];

    /**
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}