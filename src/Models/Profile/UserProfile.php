<?php

namespace FastDog\User\Models\Profile;


use Illuminate\Database\Eloquent\Model;

/**
 * Профиль пользователя
 *
 * @package FastDog\User\Models\Profile
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserProfile extends Model
{
    /**
     * Идентификатор пользователя
     * @const int
     */
    const USER_ID = 'user_id';

    /**
     * Имя
     * @const string
     */
    const NAME = 'name';

    /**
     * Фамилия
     * @const string
     */
    const SURNAME = 'surname';

    /**
     * Отчество
     * @const string
     */
    const PATRONYMIC = 'patronymic';

    /**
     * Телефон
     * @const string
     */
    const PHONE = 'phone';

    /**
     * Дополнительные данные
     * @const string
     */
    const DATA = 'data';

    /**
     * @const string
     */
    const ADDRESS = 'address';

    /**
     * Название таблицы
     *
     * @var string $table
     */
    public $table = 'users_profile';

    /**
     * Массив полей автозаполнения
     *
     * @var array $fillable
     */
    public $fillable = [self::USER_ID, self::NAME, self::SURNAME, self::PATRONYMIC, self::PHONE, self::DATA,
        self::ADDRESS];


    /**
     * Подробные данные по модели
     *
     * @return array
     */
    public function getData()
    {
        return [
            self::USER_ID => $this->{self::USER_ID},
            self::NAME => $this->{self::NAME},
            self::SURNAME => $this->{self::SURNAME},
            self::PATRONYMIC => $this->{self::PATRONYMIC},
            self::PHONE => $this->{self::PHONE},
            self::DATA => $this->{self::DATA},
            self::ADDRESS => $this->{self::ADDRESS},
        ];
    }
}
