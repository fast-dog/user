<?php

namespace FastDog\User;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class UserEventServiceProvider
 * @package FastDog\User
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserEventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'FastDog\User\Events\GetUserData' => [
            'FastDog\User\Listeners\SetUserData',//<-- обработка данных пользователя
        ],
        'FastDog\User\Events\UserAdminPrepare' => [
            'FastDog\User\Listeners\UserAdminPrepare',//<-- дополнительное событие после формирования массива с данными пользователя
            'FastDog\User\Listeners\UserItemSetEditForm',//<-- ставим форму редактирования
        ],
        //обновление профиля пользователя
        'FastDog\User\Events\UserUpdate' => [
            'FastDog\User\Listeners\UpdateProfile',
        ],
        'App\Modules\Media\Events\BeforeUploadFile' => [
            'FastDog\User\Listeners\BeforeUploadFile',
        ],
        'App\Modules\Media\Events\AfterUploadFile' => [
            'FastDog\User\Listeners\AfterUploadFile',
        ],
        'App\Modules\Media\Events\BeforeDeleteFile' => [
            'FastDog\User\Listeners\BeforeDeleteFile',
        ],
        'App\Modules\Media\Events\AfterDeleteFile' => [
            'FastDog\User\Listeners\AfterDeleteFile',
        ],
        'FastDog\User\Events\UserRegistration' => [
            'FastDog\User\Listeners\UserRegistration',
        ],
        'FastDog\User\Events\UserRating' => [
            'FastDog\User\Listeners\UserRating',
        ],
        'FastDog\User\Events\UserDelete' => [
            'FastDog\User\Listeners\UserDelete',
        ],
        'FastDog\User\Events\AddChatMessage' => [
            'FastDog\User\Listeners\AddChatMessage',
        ],
        'FastDog\User\Events\PrepareMessage' => [
            'FastDog\User\Listeners\PrepareMessage',
        ],
        'FastDog\User\Events\UserMailingAdminPrepare' => [
            'FastDog\Core\Listeners\AdminItemPrepare',// <-- Поля даты обновления и т.д.
            'FastDog\User\Listeners\UserMailingAdminPrepare',
            'FastDog\User\Listeners\UserMailingAdminSetEditorForm',
        ],
        'FastDog\User\Events\UserMailingTemplatesAdminPrepare' => [
            'FastDog\Core\Listeners\AdminItemPrepare',// <-- Поля даты обновления и т.д.
            'FastDog\User\Listeners\UserMailingTemplatesAdminPrepare',//<--
            'FastDog\User\Listeners\UserMailingTemplatesAdminSetEditorForm',//<-- ставим форму редактирования
        ],
    ];


    /**
     * @return void
     */
    public function boot()
    {
        parent::boot();


        //
    }

    public function register()
    {
        //
    }
}