<?php

namespace FastDog\User\Models\Profile;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
     * Идентификатор города\населенного пункта ФИАС
     * @const string
     */
    const CITY_ID = 'city_id';

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
        self::CITY_ID];

    /**
     * Отношение к местоположению
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function city()
    {
//        return $this->hasOne('App\Core\Location\Place', \App\Core\Location\Place::FIAS_AOGUID, self::CITY_ID);
    }

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
            self::CITY_ID => $this->{self::CITY_ID},
        ];
    }


}
