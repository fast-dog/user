<?php

namespace FastDog\User\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Настройки пользователя
 *
 * @package FastDog\User\Models
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserSettings extends Model
{
    /**
     * Идентификатор пользователя
     * @const string
     */
    const USER_ID = 'user_id';

    /**
     * Итправлять личные сообщения
     * @const string
     */
    const SEND_PERSONAL_MESSAGES = 'send_personal_messages';

    /**
     * Присылать уведомления по почте
     * @const string
     */
    const SEND_EMAIL_NOTIFY = 'send_email_notify';

    /**
     * Скрыть профиль от просмотра
     * @const string
     */
    const SHOW_PROFILE = 'show_profile';

    /**
     * Использовать поля даты\времени
     * @var bool $timestamps
     */
    public $timestamps = false;

    /**
     * Название таблицы
     * @var string $table
     */
    public $table = 'users_settings';

    /**
     * Проверка параметра
     *
     * @param $name
     * @return bool
     */
    public function can($name)
    {
        return ($this->{$name} === 1);
    }
}