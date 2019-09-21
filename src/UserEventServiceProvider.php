<?php

namespace FastDog\User;

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
        'FastDog\User\Events\UserUpdate' => [
            'FastDog\User\Listeners\UpdateProfile',// <-- обновление профиля пользователя
        ],
        'App\Modules\Media\Events\BeforeUploadFile' => [
            'FastDog\User\Listeners\BeforeUploadFile',// <-- перед загрузкой файла (фото пользователя)
        ],
        'App\Modules\Media\Events\AfterUploadFile' => [
            'FastDog\User\Listeners\AfterUploadFile',// <-- после загрузки файла (фото пользователя)
        ],
        'App\Modules\Media\Events\BeforeDeleteFile' => [
            'FastDog\User\Listeners\BeforeDeleteFile',// <-- перед удалением файла (фото пользователя)
        ],
        'App\Modules\Media\Events\AfterDeleteFile' => [
            'FastDog\User\Listeners\AfterDeleteFile',// <-- после удаления файла (фото пользователя)
        ],
        'FastDog\User\Events\UserRegistration' => [
            'FastDog\User\Listeners\UserRegistration',// <-- регистрация пользователя, отправка писем и т.д.
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
        'FastDog\Core\Events\GetComponentType' => [
            'FastDog\User\Listeners\GetComponentType',// <-- Добавляем типы в список компонентов
        ],
        'FastDog\Menu\Events\MenuResources' => [
            'FastDog\User\Listeners\MenuResources',  //<-- добавление ресурсов для создания меню
        ],
    ];


    /**
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
