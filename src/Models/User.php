<?php

namespace FastDog\User\Models;


use FastDog\Core\Models\BaseModel;
use FastDog\Core\Models\DomainManager;
use FastDog\Core\Table\Filters\BaseFilter;
use FastDog\Core\Table\Filters\Operator\BaseOperator;
use FastDog\Core\Table\Interfaces\TableModelInterface;
use FastDog\Media\Models\Gallery;
use FastDog\User\Events\GetUserData;
use FastDog\User\Events\UserAdminPrepare;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Jenssegers\Date\Date;

/**
 * Модель пользователей
 *
 * @package FastDog\User\Models
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class User extends Authenticatable implements TableModelInterface
{
    use Notifiable, SoftDeletes;

    /**
     * Тип учетной записи: администратор
     * @const string
     */
    const USER_TYPE_ADMIN = 'admin';

    /**
     * Тип учетной записи: пользователь
     * @const string
     */
    const USER_TYPE_USER = 'user';

    /**
     * Тип учетной записи: Юридическое лицо\корпоративный клиент и т.д.
     * @const string
     */
    const USER_TYPE_CORPORATE = 'corporate';

    /**
     * Тип учетной записи: дилер
     * @const string
     */
    const USER_TYPE_DEALER = 'dealer';

    /*
     * Возможные состояния учетной записи
     *
     * status enum ('not-confirmed', 'active', 'restore-password', 'banned') NOT NULL DEFAULT 'not-confirmed',
     */
    const STATUS = 'status';
    /**
     * Состояние учетной записи: аккаунт активирован
     * @const string
     */
    const STATUS_ACTIVE = 'active';

    /**
     * Состояние учетной записи: аккаунт не подтвержден
     * @const string
     */
    const STATUS_NOT_CONFIRMED = 'not-confirmed';

    /**
     * Состояние учетной записи: производится восстановление пароля
     * @const string
     */
    const STATUS_RESTORE_PASSWORD = 'restore-password';

    /**
     * Состояние учетной записи: заблокирован
     * @const string
     */
    const STATUS_BANNED = 'banned';

    /**
     * Код сайта в формате ХХХ
     * @const string
     */
    const SITE_ID = 'site_id';

    /**
     * Дополнительные параметры модели
     *
     * @const string
     */
    const DATA = 'data';

    /**
     * Тип учетной записи
     * @const string
     */
    const TYPE = 'type';

    /**
     * Email
     * @const string
     */
    const EMAIL = 'email';

    /**
     * Хэш пароля
     * @const string
     */
    const PASSWORD = 'password';

    /**
     * Яхык по умолчанию
     * @const string
     */
    const LANG = 'lang';
    /**
     * Хэш активации
     * @const string
     */
    const HASH = 'hash';


    /**
     * Уникальный идентификатор пользователя
     * @const string
     */
    const LOGIN = 'login';

    /**
     * Ключ кэширование данных по аватарам пользователей
     *
     * @const string
     */
    const CACHE_KEY_PHOTO = 'user#photo';

    /**
     * Ключ кэширование данных для отображения в позиции каталога
     * @const string
     */
    const CACHE_KEY_ITEM_DATA = 'user:item-data';

    /**
     * Время последней активности пользователя
     * @const string
     */
    const  LAST_VISIT = 'last_visit';

    /**
     * Период времени в который пользователь считается онлайн
     * @const int
     */
    const PERIOD_ONLINE = 5;

    /**
     * Идентификатор служебного аккаунта
     *
     * @const integer
     */
    const SERVICE_ACCOUNT_ID = 7;

    /**
     * Заглушка если нет фото
     *
     * @const string
     */
    const NO_PHOTO = '/vendor/fast_dog/user/img/no-photo.png';

    /**
     * Массив полей автозаполнения
     *
     * @var array $fillable
     */
    protected $fillable = [
        self::EMAIL, self::PASSWORD, self::TYPE, self::DATA, self::SITE_ID, self::STATUS,
        self::LANG, self::HASH, self::LAST_VISIT,
    ];

    /**
     * Скрытые поля
     * @var array $hidden
     */
    public $hidden = ['password', 'remember_token',];

    /**
     * @var array
     */
    public $dates = [self::LAST_VISIT];
    /**
     * Профиль
     *
     * @var mixed
     */
    protected static $profile;

    /**
     * Признак присутсвия пользователя
     *
     * @var boolean $is_online
     */
    public $is_online;

    /**
     * Подробные данные по модели
     *
     * Для получения дополнительных данных вызывается событие GetUserData
     *
     * @param bool $fireEvent
     * @return array
     * @see \FastDog\User\Listeners\SetUserData::handle()
     *
     */
    public function getData($fireEvent = true)
    {
        if ($this->data <> null && is_string($this->data)) {
            $this->data = json_decode($this->data);
        }

        $data = [
            'id' => (int)$this->id,
            self::EMAIL => $this->{self::EMAIL},
            self::TYPE => $this->{self::TYPE},
            self::SITE_ID => $this->{self::SITE_ID},
            self::DATA => $this->{self::DATA},
            self::STATUS => $this->{self::STATUS},
            self::GROUP_ID => $this->{self::GROUP_ID},
        ];

        event(new GetUserData($data, $this));

        return $data;
    }


    /**
     * Ссылка на модель профиля
     *
     * В зависимоти от типа учетной записи будет возвращать модель профилья
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @see  \FastDog\User\Models\Profile\UserProfile::getData()
     *
     */
    public function profile()
    {
        switch ($this->type) {
            case self::USER_TYPE_ADMIN:
                return $this->hasOne('FastDog\User\Models\Profile\UserProfile', 'user_id', 'id');
            case self::USER_TYPE_USER:
                return $this->hasOne('FastDog\User\Models\Profile\UserProfile', 'user_id', 'id');
            case self::USER_TYPE_CORPORATE:
                return $this->hasOne('FastDog\User\Models\Profile\UserProfileCorporate', 'user_id', 'id');
        }
    }

    /**
     * Отношение к настройкам пользователя
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function setting()
    {
        return $this->hasOne('FastDog\User\Models\UserSettings', 'user_id', 'id');
    }


    /**
     * Текущая валюта
     *
     * Возвращает валюту  выбранную пользователем (автоматически)
     *
     * @return mixed
     */
    public static function getCurrency()
    {
        $currency = [
            'ru' => 'rub', 'en' => 'eur', 'us' => 'us',
        ];
        $cur = Session::get('user_currency');

        if ($cur == null) {
            $cur = $currency[\App::getLocale()];
            //Session::set('user_currency', $cur);
        }

        return $cur;
    }


    /**
     * Фото профиля
     *
     * @param $resize bool|string '100x100'
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getPhoto($resize = false)
    {
        $this->data = $this->getAttribute(self::DATA);

        if ($this->data <> null && is_string($this->data)) {
            $this->data = json_decode($this->data);
        }
        $key = self::CACHE_KEY_PHOTO . $this->id;
        if ($resize) {
            $key .= (string)$resize;
        }
        $isRedis = config('cache.default') == 'redis';
        $result = ($isRedis) ? \Cache::tags(['user#' . $this->id])->get($key, null) : \Cache::get($key, null);
        if (null === $result) {
            if (isset($this->data->photo_id)) {
                $file = GalleryItem::where('id', $this->data->photo_id)->first();
                if ($file) {
                    $file->data = json_decode($file->data);
                    if ($resize !== false) {
                        $file->data->thumb = (object)Gallery::getPhotoThumb($file->data->file, $resize);
                    }
                    if (isset($file->data->thumb)) {
                        $result = url($file->data->thumb->file);
                    } else {
                        $result = url($file->path);
                    }
                }
            } else {
                if ($resize !== false) {
                    $thumbnail = Gallery::getPhotoThumb(self::NO_PHOTO, $resize);
                    if ($thumbnail['exist']) {
                        $result = url($thumbnail['file']);
                    }
                } else {
                    $result = url(self::NO_PHOTO);
                }
            }
            if (null === $result) {
                if ($resize !== false) {
                    $thumbnail = Gallery::getPhotoThumb(self::NO_PHOTO, $resize);
                    if ($thumbnail['exist']) {
                        $result = url($thumbnail['file']);
                    }
                } else {
                    $result = url(self::NO_PHOTO);
                }
            }
            if ($isRedis) {
                \Cache::tags(['user#' . $this->id])->put($key, $result, config('cache.ttl_user', 10));
            } else {
                \Cache::put($key, $result, config('cache.ttl_user', 10));
            }
        }

        return $result;
    }

    /**
     * Отформатированное ФИО
     *
     * @return mixed|string
     */
    public function getName()
    {
        if (isset($this->profile) && $this->profile) {
            $name = $this->profile->name;
            if ($name) {
                return $name . ' ' . $this->profile->surname;
            }
        }

        return $this->email;
    }


    /**
     * Тип пользователя
     *
     * @return string
     */
    public function getRoleName()
    {
        switch ($this->type) {
            case self::USER_TYPE_ADMIN:
                return 'Администратор';
            case self::USER_TYPE_USER:
                return 'Пользователь';
            case self::USER_TYPE_CORPORATE:
                return 'Юр. лицо';
        }
    }

    /**
     * Типы учетных записей
     *
     * !!! не путать с ролями ACL
     *
     * @return array
     */
    public static function getAllType()
    {
        $result = [
            ['id' => self::USER_TYPE_ADMIN, 'name' => 'Администратор'],
            ['id' => self::USER_TYPE_USER, 'name' => 'Пользователь'],
            ['id' => self::USER_TYPE_CORPORATE, 'name' => 'Юр. лицо'],
        ];

        return $result;
    }

    /**
     * Типы юр.лица
     *
     * @return array
     */
    public static function getAllTypeCorporate()
    {
        $result = [
            ['id' => '0', 'name' => 'ООО'],
            ['id' => '1', 'name' => 'ИП'],
            ['id' => '2', 'name' => 'ЗАО'],
            ['id' => '3', 'name' => 'ОАО'],
        ];

        return $result;
    }

    /**
     * Доступные для регистрации страны
     *
     * @return array
     */
    public static function getAllCountry()
    {
        $result = [
            ['id' => '7', 'name' => 'Россия'],
        ];

        return $result;
    }


    /**
     * Возвращает возможные состояния
     *
     * @return array
     */
    public static function getStatusList()
    {
        return [
            ['id' => self::STATUS_ACTIVE, 'name' => 'Активировано'],
            ['id' => self::STATUS_NOT_CONFIRMED, 'name' => 'Не подтверждена'],
            ['id' => self::STATUS_RESTORE_PASSWORD, 'name' => 'Восстановление пароля'],
            ['id' => self::STATUS_BANNED, 'name' => 'Заблокирована'],
        ];
    }

    /**
     * Текущий язык контента
     *
     * @return mixed
     */
    public function getCurrentLanguage()
    {
        return $this->{self::LANG};
    }

    /**
     * Локализация приложения
     *
     * @return mixed
     */
    public function getCurrentLanguageTranslation()
    {
        return trans('app');
    }

    /**
     * Статистика модели
     *
     * Включает в себя кол-во зарегистрированных\удаленных и т.д.
     *
     * @param $fire_event
     * @return array
     */
    public static function getStatistic($fire_event = true)
    {
        $countActive = self::where(function (Builder $query) {
            $query->where(self::STATUS, self::STATUS_ACTIVE);
            $query->where(self::SITE_ID, DomainManager::getSiteId());
        })->count();

        $countNotConfirmed = self::where(function (Builder $query) {
            $query->where(self::STATUS, self::STATUS_NOT_CONFIRMED);
            $query->where(self::SITE_ID, DomainManager::getSiteId());
        })->count();

        $countBanned = self::where(function (Builder $query) {
            $query->where(self::STATUS, self::STATUS_BANNED);
            $query->where(self::SITE_ID, DomainManager::getSiteId());
        })->count();

        $countDeleted = self::where(function (Builder $query) {
            $query->where(self::SITE_ID, DomainManager::getSiteId());
        })->whereNotNull('deleted_at')->withTrashed()->count();

        $total = self::where(function (Builder $query) {
            $query->where(self::SITE_ID, DomainManager::getSiteId());
        })->withTrashed()->count();

        $result = [
            'total' => $total,
            'active' => $countActive,
            'active_percent' => round((($countActive * 100) / $total), 2),
            'not_confirmed' => $countNotConfirmed,
            'not_confirmed_percent' => round((($countNotConfirmed * 100) / $total), 2),
            'in_trash' => $countBanned,
            'in_trash_percent' => round((($countBanned * 100) / $total), 2),
            'deleted' => $countDeleted,
            'deleted_percent' => round((($countDeleted * 100) / $total), 2),
            'cache_tags' => (env('CACHE_DRIVER') === 'redis') ? 'Y' : 'N',
        ];

        return $result;
    }

    /**
     * Генерация пароля
     *
     * @param int $length
     * @return string
     */
    public static function quickRandom($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

    /**
     * Информация для отображения в профиле
     *
     * @return array
     */
    public function getProfileData()
    {
        $result = [
            'address' => $this->getAddress(),
            'age' => $this->getAge(),
            'children' => $this->getChildren(),
            'about' => $this->getAbout(),
            'location' => $this->getLocation(),
        ];

        return $result;
    }

    /**
     * Получение строки адреса пользователя
     *
     * @return array|null|string
     */
    public function getAddress()
    {
        $result = null;

        /**
         * @var $userLocality BaseModel
         */
        $userLocality = $this->profile->city;

        if ($userLocality) {
            $result = [Str::title($userLocality->{BaseModel::NAME})];

            return implode(', ', $result);
        }

        return $result;
    }

    /**
     * Возвращает кол-во лет прошедших с даты рождения пользователя
     *
     * @return int|null
     */
    public function getAge()
    {
        if ($this->profile->birth) {
            return Carbon::now()->diffInYears($this->profile->birth);
        }

        return null;
    }


    /**
     * Ссылка на профиль пользователя
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getPublicLink()
    {
        if ($this->setting->can(UserSettings::SHOW_PROFILE) === true) {
            return null;
        }
        if ($this->{self::LOGIN}) {
            return url('/user/' . $this->{self::LOGIN});
        }

        return url('/user/' . $this->id);
    }

    /**
     * возвращает интервал от переданной даты
     *
     * @param $timestamp
     * @return string
     */
    public function getDateDiff($timestamp)
    {
        $date = new Date('now');

        $lang = $date->getTranslator();

        $interval = (array)$date->diff(new Date($timestamp));
        $units = ['y' => 'year', 'm' => 'month', 'w' => 'week', 'd' => 'day', 'h' => 'hour', 'i' => 'minute', 's' => 'second'];
        $str = [];
        $interval['w'] = (int)($interval['d'] / 7);
        $interval['d'] = $interval['d'] % 7;

        foreach ($units as $k => $unit) {
            if ($interval[$k]) {
                $str[] = $lang->transChoice("$unit", $interval[$k], [':count' => $interval[$k]]);
            }
        }
        if (count($str) > 2) {
            $str = array_slice($str, 0, 2);
        }

        return implode(', ', $str);
    }


    /**
     * Определение онлайн пользователя
     *
     * @return bool
     */
    public function isOnline()
    {
        if ($this->id == self::SERVICE_ACCOUNT_ID) {
            return true;
        }
        if ($this->last_visit) {
            return ($this->last_visit->diffInMinutes(Carbon::now()) > self::PERIOD_ONLINE) ? false : true;
        }

        return false;

    }

    /**
     * @param $format
     * @return string
     */
    public function getLastVisit($format)
    {
        Carbon::setLocale('ru');
        Date::setLocale(config('app.locale'));
        if ($this->id == self::SERVICE_ACCOUNT_ID) {
            return Carbon::now()->format($format);
        }

        return Date::createFromFormat(Carbon::DEFAULT_TO_STRING_FORMAT, $this->last_visit)->format($format);

        //return $this->last_visit->format($format);
    }

    /**
     * Метатеги страницы профиля
     *
     * @return array
     */
    public function getProfileMetatag()
    {
        return [
            'title' => trans('public.Профиль пользователя: :name', ['name' => $this->getName()]),
        ];
    }

    /**
     * Ссылка на диалог с пользователем
     *
     * @param $user_id
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public static function getUserConversationLink($user_id)
    {
        /**
         * @var $messageManager MessageManager
         */
        $messageManager = \App::make(MessageManager::class);
        //получаем ID чата с пользователем
        $conversationId = $messageManager->isConversationExists($user_id);

        if ($conversationId !== false) {
            return url('cabinet/messages?con=' . $conversationId);
        }

        return url('cabinet/messages?user_id=' . $user_id);
    }

    /**
     * Проверка находится ли пользователь в закладках
     *
     * @param $user_id
     * @return mixed
     */
    public static function checkUserInFavorites($user_id)
    {
        if (!\Auth::guest()) {
            /**
             * @var $user self
             */
            $user = \Auth::getUser();

            return UserFavorites::where([
                    UserFavorites::USER_ID => $user->id,
                    UserFavorites::ITEM_ID => $user_id,
                ])->count() > 0;
        }

        return false;
    }

    /**
     *
     * @return bool
     */
    public function isVerifiedUser()
    {
        return false;
    }

    /**
     * Обертка для определения онлайн
     *
     * @return array
     */
    public function toArray()
    {
        $result = parent::toArray();
        $result['is_online'] = $this->isOnline() ? 1 : 0;
        $result['photo'] = $this->getPhoto('50');
        $result['name'] = $this->getName();

        return $result;
    }


    /**
     * Возвращает имя события вызываемого при обработке данных при передаче на клиент в разделе администрирования
     * @return string
     */
    public function getEventAdminPrepareName(): string
    {
        return UserAdminPrepare::class;
    }

    /**
     * Возвращает описание доступных полей для вывода в колонки...
     *
     * ... метод используется для первоначального конфигурирования таблицы,
     * дальнейшие типы, порядок колонок и т.д. будут храниться в обхекте BaseTable
     *
     * @return array
     */
    public function getTableCols(): array
    {
        return [
            [
                'name' => 'Email',
                'key' => User::EMAIL,
                'domain' => true,
                'link' => 'user_profile',
            ],
            [
                'name' => trans('app.Дата регистрации'),
                'key' => User::CREATED_AT,
                'width' => 150,
                'link' => null,
                'class' => 'text-center',
            ],
            [
                'name' => '#',
                'key' => 'id',
                'link' => null,
                'width' => 80,
                'class' => 'text-center',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getAdminFilters(): array
    {
        $default = [
            [
                [
                    BaseFilter::NAME => \FastDog\User\Models\User::EMAIL,
                    BaseFilter::PLACEHOLDER => 'Email',
                    BaseFilter::TYPE => BaseFilter::TYPE_TEXT,
                    BaseFilter::DISPLAY => false,
                    BaseFilter::OPERATOR => (new BaseOperator('LIKE', 'LIKE'))->getOperator(),

                ],
                BaseFilter::getLogicAnd(),
                [
                    BaseFilter::TYPE => BaseFilter::TYPE_SELECT,
                    BaseFilter::NAME => User::STATUS,
                    BaseFilter::DISPLAY => true,
                    BaseFilter::PLACEHOLDER => trans('app.Состояние'),
                    BaseFilter::DATA => User::getStatusList(),
                    BaseFilter::OPERATOR => (new BaseOperator())->getOperator(),
                ],
            ],
            [
                BaseFilter::getLogicAnd(),
                [
                    BaseFilter::TYPE => BaseFilter::TYPE_DATETIME,
                    BaseFilter::NAME => User::CREATED_AT,
                    BaseFilter::DISPLAY => true,
                    BaseFilter::PLACEHOLDER => trans('app.Дата регистрации'),
                    BaseFilter::OPERATOR => (new BaseOperator('BETWEEN', 'BETWEEN'))->getOperator(
                        [['id' => 'BETWEEN', 'name' => 'BETWEEN']]
                    ),
                ],
            ],
        ];

        return $default;
    }

    /**
     * Возвращает ключ доступа к ACL
     * @param string $type
     * @return string
     */
    public function getAccessKey($type = 'guest'): string
    {
        return strtolower(\FastDog\User\User::class) . '::' . DomainManager::getSiteId() . '::' . $type;
    }
}