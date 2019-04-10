<?php

namespace FastDog\User;


use FastDog\Core\Interfaces\ModuleInterface;
use FastDog\Core\Models\DomainManager;
use FastDog\User\Controllers\Site\CabinetController;
use FastDog\User\Controllers\Site\UserController;
use FastDog\User\Models\User as UserModel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

/**
 * Пользователи
 *
 * @package FastDog\User
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class User extends UserModel implements ModuleInterface
{

    /**
     * Имя родительского списка доступа
     *
     * из за реализации ACL в пакете kodeine/laravel-acl
     * нужно использовать имя верхнего уровня: action.__CLASS__::SITE_ID::access_level
     *
     *
     * @var string
     */
    protected $aclName = '';

    /**
     * Идентификатор модуля
     *
     * @const string
     */
    const MODULE_ID = 'user';

    /**
     * Маршрут: авторизация
     *
     * @const string
     */
    const TYPE_LOGIN = 'user_login';

    /**
     * Маршрут: выход
     *
     * @const string
     */
    const TYPE_LOGOUT = 'user_logout';

    /**
     * Маршрут: регистрация
     *
     * @const string
     */
    const TYPE_REGISTRATION = 'user_registration';

    /**
     * Маршрут: восстановление доступа
     *
     * @const string
     */
    const TYPE_RESTORE_PASSWORD = 'user_restore_password';

    /**
     * Маршрут: личный кабинет
     *
     * @const string
     */
    const TYPE_CABINET = 'user_cabinet';

    /**
     * Маршрут: личный кабинет (редактирование данных профиля)
     *
     * @const string
     */
    const TYPE_CABINET_EDIT = 'user_cabinet_edit';

    /**
     * Маршрут: личный кабинет - настройки
     *
     * @const string
     */
    const TYPE_CABINET_SETTINGS = 'user_cabinet_settings';

    /**
     * Маршрут: личный кабинет - сообщения
     *
     * @const string
     */
    const TYPE_CABINET_MESSAGES = 'user_cabinet_messages';

    /**
     * Маршрут: личный кабинет - новое сообщение
     *
     * @const string
     */
    const TYPE_CABINET_NEW_MESSAGES = 'user_cabinet_new_messages';


    /**
     * Маршрут: личный кабинет - мои объявления
     * @const string
     */
    const TYPE_CABINET_MY_ITEMS = 'user_cabinet_my_items';

    /**
     * Маршрут: личный кабинет - список покупок
     * @const string
     */
    const TYPE_CABINET_MY_BUYING = 'user_cabinet_my_buying';

    /**
     * Маршрут: личный кабинет - закладки|избранное
     * @const string
     */
    const TYPE_CABINET_FAVORITES = 'user_cabinet_favorites';

    /**
     * Маршрут: личный кабинет - счет
     * @const string
     */
    const TYPE_CABINET_BILLING = 'user_cabinet_billing';

    /**
     * Маршрут: Личный кабинет - выбор покупателя на товар
     * @const string
     */
    const TYPE_CABINET_CHOOSE_BUYER = 'user_cabinet_choose_buyer';

    /**
     * Маршрут: Пользователи :: Добавление отзыва продавцу
     * @const string
     */
    const TYPE_CABINET_ADD_OPINION = 'user_cabinet_add_opinion';

    /**
     * Маршрут: Пользователи :: Добавление отзыва покупателю
     * @const string
     */
    const TYPE_CABINET_ADD_OPINION_BUYER = 'user_cabinet_add_opinion_buyer';

    /**
     * Маршрут: Блог
     * @const string
     */
    const TYPE_BLOG = 'user_blog';

    /**
     * @const string
     */
    const SETTINGS = 'settings';

    /**
     * Параметры конфигурации описанные в module.json
     *
     * @var null|object $data
     */
    protected $data;


    /**
     * События обрабатываемые модулем
     *
     * @return array
     */
    public function initEvents(): array
    {

    }


    /**
     * Доступные шаблоны
     *
     * @param  $paths
     * @return null|array
     */
    public function getTemplates($paths = ''): array
    {
        $result = [];

        //получаем доступные пользователю site_id
        $domainsCode = DomainManager::getScopeIds();

        $list = DomainManager::getAccessDomainList();
        foreach ($domainsCode as $code) {
            $_code = $code;
            $currentPath = str_replace('modules', 'public/' . $code . '/modules', $paths);
            if (isset($list[$code])) {
                $code = $list[$code]['name'];
            }
            if ($currentPath !== '') {
                $description = [];
                if (file_exists(dirname($currentPath) . '/.description.php') && $description == []) {
                    $description = include dirname($currentPath) . '/.description.php';
                }
                foreach (glob($currentPath) as $filename) {
                    if (!isset($result[$code])) {
                        $result[$code]['templates'] = [];
                    }
                    $tmp = explode('/', $filename);

                    $count = count($tmp);
                    if ($count >= 2) {
                        $search = array_search($_code, $tmp);
                        if ($search) {
                            $tmp = array_slice($tmp, $search + 1, $count);
                        }
                        $templateName = implode('.', $tmp);

                        $templateName = str_replace(['.blade.php'], [''], $templateName);
                        $name = array_last(explode('.', $templateName));

                        if (isset($description[$name])) {
                            $name = $description[$name];
                        }
                        $id = 'theme#' . $_code . '::' . $templateName;
                        $trans_key = str_replace(['.', '::'], '/', $id);

                        array_push($result[$code]['templates'], [
                            'id' => $id,
                            'name' => $name,
                            'translate' => Translate::getSegmentAdmin($trans_key),
                            'raw' => File::get(view($id)->getPath()),
                        ]);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Возвращает доступные типы меню
     *
     * @return null|array
     */
    public function getMenuType()
    {
        return [
            ['id' => 'user_login', 'name' => 'Пользователи :: Авторизация', 'sort' => 400],
            ['id' => 'user_registration', 'name' => 'Пользователи :: Регистрация', 'sort' => 401],
            ['id' => 'user_restore_password', 'name' => 'Пользователи :: Восстановление пароля', 'sort' => 402],
            ['id' => 'user_cabinet', 'name' => 'Пользователи :: Личный кабинет', 'sort' => 403],
            ['id' => 'user_cabinet_edit', 'name' => 'Пользователи :: Личный кабинет (редактирование)', 'sort' => 404],
            ['id' => 'user_cabinet_settings', 'name' => 'Пользователи :: Личный кабинет - настройки', 'sort' => 450],
            ['id' => 'user_cabinet_messages', 'name' => 'Пользователи :: Личный кабинет - сообщения', 'sort' => 460],
            ['id' => 'user_cabinet_new_messages', 'name' => 'Пользователи :: Личный кабинет - новое сообщение', 'sort' => 470],
            ['id' => 'user_cabinet_favorites', 'name' => 'Пользователи :: Личный кабинет - закладки', 'sort' => 480],
            ['id' => 'user_cabinet_billing', 'name' => 'Пользователи :: Личный кабинет - счет', 'sort' => 490],
            ['id' => 'user_logout', 'name' => 'Пользователи :: Выход', 'sort' => 400],
        ];
    }

    /**
     * Возвращает информацию о модуле
     *
     * @param bool $includeTemplates
     * @return array
     */
    public function getModuleInfo($includeTemplates = true)
    {
        $result = [];
        $paths = array_first(\Config::get('view.paths'));
        $templates_paths = array_first($this->data->{'templates_paths'});
        if (isset($this->data->menu)) {
            foreach ($this->data->menu as $item) {
                if (isset($item->id)) {
                    $templates = [];
                    if ($includeTemplates && isset($templates_paths->{$item->id})) {
                        $templates = $this->getTemplates($paths . $templates_paths->{$item->id});
                    }
                    array_push($result, [
                        'id' => $item->id,
                        'name' => $item->name,
                        'templates' => $templates,
                        'class' => __CLASS__,
                    ]);
                }
            }
        }

        return $result;
    }

    /**
     * Устанавливает параметры в контексте объекта
     *
     * @param $data
     * @return mixed
     */
    public function setConfig($data)
    {
        $this->data = $data;
    }

    /**
     *  Возвращает параметры объекта
     *
     * @return mixed
     */
    public function getConfig()
    {
        return $this->data;
    }

    /**
     * Возвращает возможные типы меню
     *
     * @return mixed
     */
    public function _getPublicMenuType()
    {
        return [];
    }

    /**
     * Возвращает возможные типы модулей
     *
     * @return mixed
     */
    public function getModuleType()
    {
        $paths = array_first(\Config::get('view.paths'));

        $result = [
            'id' => 'users',
            'instance' => __CLASS__,
            'name' => trans('app.Пользователи'),
            'items' => [
                [
                    'id' => 'messages',
                    'name' => trans('app.Пользователи') . ' :: ' . trans('app.Личные сообщения'),
                    'templates' => $this->getTemplates($paths . '/modules/users/messages/*.blade.php'),
                ],
                [
                    'id' => 'login',
                    'name' => trans('app.Пользователи') . ' :: ' . trans('app.Авторизация пользователя'),
                    'templates' => $this->getTemplates($paths . '/modules/users/auth/*.blade.php'),
                ],
                [
                    'id' => 'registration',
                    'name' => trans('app.Пользователи') . ' :: ' . trans('app.Регистрация пользователя'),
                    'templates' => $this->getTemplates($paths . '/modules/users/registration/*.blade.php'),
                ],
                [
                    'id' => 'subscribe',
                    'name' => trans('app.Пользователи') . ' :: ' . trans('app.Подписка на рассылку'),
                    'templates' => $this->getTemplates($paths . '/modules/users/subscribe/*.blade.php'),
                ],
            ],
        ];

        return $result;
    }


    /**
     * Возвращает маршрут компонента
     *
     * @param Request $request
     * @param Menu $item
     * @return mixed
     */
    public function getMenuRoute($request, $item)
    {
        $result = [];
        if (isset($item->data->type)) {
            switch ($item->data->type) {
                case self::TYPE_LOGIN:
                    array_push($result, 'login');
                    break;
                case self::TYPE_LOGOUT:
                    array_push($result, 'logout');
                    break;
                case self::TYPE_CABINET:
                    array_push($result, $request->input(Menu::ALIAS));

                    return [
                        'type' => $item->data->type,
                        'instance' => CabinetController::class,
                        'route' => implode('/', $result),
                    ];
                case self::TYPE_CABINET_SETTINGS:
                    $roots = $item->getAncestors();
                    foreach ($roots as $root) {
                        if ($root->alias && !in_array($root->alias, ['#', '/'])) {
                            array_push($result, $root->alias);
                        }
                    }

                    if ($request->input(Menu::ALIAS, null)) {
                        array_push($result, $request->input(Menu::ALIAS, null));
                    } else {
                        array_push($result, 'settings');
                    }

                    return [
                        'type' => $item->data->type,
                        'instance' => CabinetController::class,
                        'route' => implode('/', $result),
                    ];
                case self::TYPE_CABINET_MESSAGES:
                    if ($item->parent) {
                        array_push($result, $item->parent->alias);
                    }

                    if ($request->input(Menu::ALIAS, null)) {
                        array_push($result, $request->input(Menu::ALIAS, null));
                    } else {
                        array_push($result, 'messages');
                    }

                    return [
                        'type' => $item->data->type,
                        'instance' => CabinetController::class,
                        'route' => implode('/', $result),
                    ];
                case self::TYPE_CABINET_NEW_MESSAGES:
                    if ($item->parent) {
                        array_push($result, $item->parent->alias);
                    }
                    if ($request->input(Menu::ALIAS, null)) {
                        array_push($result, $request->input(Menu::ALIAS, null));
                    } else {
                        array_push($result, 'new-messages');
                    }

                    return [
                        'type' => $item->data->type,
                        'instance' => CabinetController::class,
                        'route' => implode('/', $result),
                    ];
                case self::TYPE_REGISTRATION:
                    array_push($result, 'registration');
                    break;
                case self::TYPE_RESTORE_PASSWORD:
                    array_push($result, 'restore-password');
                    break;
                default:
                    if ($item->parent) {
                        array_push($result, $item->parent->alias);
                    }
                    if ($request->input(Menu::ALIAS, null)) {
                        array_push($result, $request->input(Menu::ALIAS, null));
                    } else {
                        array_push($result, $item->alias);
                    }

                    return [
                        'type' => $item->data->type,
                        'instance' => CabinetController::class,
                        'route' => implode('/', $result),
                    ];
                    break;
            }

            return [
                'type' => (isset($item->data->type)) ? $item->data->type : 'undefined',
                'instance' => UserController::class,
                'route' => implode('/', $result),
            ];
        }

        return null;
    }


    /**
     * Инициализация уровней доступа ACL
     *
     * @return null
     */
    public function initAcl()
    {
        $domainList = DomainManager::getAccessDomainList();
        foreach ($domainList as $domain) {
            if ($domain['id'] !== '000') {
                /**
                 * Имя раздела разрешений должно быть в нижнем регистре из за
                 * особенностей реализации методов в пакете kodeine/laravel-acl
                 */
                $name = strtolower(__CLASS__ . '::' . $domain['id']);

                $roleGuest = DomainManager::getRoleGuest($domain['id']);
                $data = [
                    'name' => $name,
                    'slug' => [
                        'create' => false,
                        'view' => false,
                        'update' => false,
                        'delete' => false,
                        'api' => false,
                    ],
                    'description' => \GuzzleHttp\json_encode([
                        'module_name' => 'Пользователи',
                        'description' => 'ACL для домена #' . $domain['id'],
                    ]),
                ];
                $permGuest = Permission::where([
                    'name' => $data['name'] . '::guest',
                ])->first();

                if (!$permGuest) {
                    $data['name'] = $name . '::guest';
                    $permGuest = Permission::create($data);
                    $roleGuest->assignPermission($permGuest);
                } else {
                    Permission::where('id', $permGuest->id)->update([
                        'slug' => json_encode($data['slug']),
                    ]);
                }
                $permUser = Permission::where([
                    'name' => $data['name'] . '::user',
                ])->first();
                if (!$permUser) {
                    $data['inherit_id'] = $permGuest->id;
                    $data['name'] = $name . '::user';
                    $permUser = Permission::create($data);
                } else {
                    Permission::where('id', $permUser->id)->update([
                        'slug' => json_encode($data['slug']),
                    ]);
                }
                if ($permUser) {
                    $roleUser = DomainManager::getRoleUser($domain['id']);
                    if ($roleUser) {
                        $roleUser->assignPermission($permUser);
                    }

                    $roleAdmin = DomainManager::getRoleAdmin($domain['id']);
                    $data['slug'] = [
                        'view' => true,
                        'create' => true,
                        'update' => true,
                        'delete' => true,
                        'api' => true,
                    ];
                    $permAdmin = Permission::where([
                        'name' => $data['name'] . '::admin',
                    ])->first();
                    if (!$permAdmin) {
                        $data['name'] = $name . '::admin';
                        $data['inherit_id'] = $permUser->id;
                        $permAdmin = Permission::create($data);
                        $roleAdmin->assignPermission($permAdmin);
                    } else {
                        Permission::where('id', $permAdmin->id)->update([
                            'slug' => json_encode($data['slug']),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Метод возвращает отображаемый в публичной части контнет
     *
     * @param Components $module
     * @return null|string
     * @throws \Throwable
     */
    public function getContent(Components $module)
    {
        $result = '';

        $data = $module->getData();

        if (isset($data['data']->type)) {
            switch ($data['data']->type) {
                case 'users::messages':
                    /**
                     * @var $messageManager MessageManager
                     */
                    $messageManager = \App::make(MessageManager::class);
                    $unread = $messageManager->getUnreadCount();

                    if (isset($data['data']->template->id) && view()->exists($data['data']->template->id)) {
                        return view($data['data']->template->id, [
                            'module' => $module,
                            'unread' => $unread,
                            'items' => $messageManager->getInboxNew(),
                        ])->render();
                    }
                    break;
                default:
                    if (isset($data['data']->template->id) && view()->exists($data['data']->template->id)) {
                        return view($data['data']->template->id, [
                            'module' => $module,
                        ])->render();
                    }
                    break;
            }
        }

        return $result;
    }

    /**
     * Метод возвращает директорию модуля
     *
     * @return string
     */
    public function getModuleDir()
    {
        return dirname(__FILE__);
    }

    /**
     * Возвращает параметры блоков добавляемых на рабочий стол администратора
     *
     * @return array
     */
    public function getDesktopWidget()
    {
        return [];
    }

    /**
     * Параметры публичного раздела
     *
     * Возвращает параметры публичного раздела
     *
     * @return UserConfig
     */
    public function getPublicConfig()
    {
        $key = __METHOD__ . '::' . DomainManager::getSiteId() . '::module-users-public';
        $isRedis = config('cache.default') == 'redis';

        $config = ($isRedis) ? \Cache::tags(['config'])->get($key, null) : \Cache::get($key, null);
        if (null === $config) {
            /**
             * @var $config UserConfig
             */
            $config = UserConfig::where(UserConfig::ALIAS, UserConfig::CONFIG_PUBLIC)->first();

            if ($isRedis) {
                \Cache::tags(['config'])->put($key, $config, config('cache.ttl_config', 5));
            } else {
                \Cache::put($key, $config, config('cache.ttl_config', 5));
            }
        }

        return $config;
    }

    /**
     * Схема установки модуля
     *
     * @param $allSteps
     * @return mixed
     */
    public function getInstallStep(&$allSteps)
    {
        $last = array_last(array_keys($allSteps));

        $allSteps[$last]['step'] = 'user_init';
        $allSteps['user_init'] = [
            'title_step' => trans('app.Модуль Пользователи: создание таблиц'),
            'step' => 'user_table',
            'stop' => false,
            'install' => function ($request) {
                User::createDbSchema();
                UserEmails::createDbSchema();
                sleep(1);
            },
        ];

        $allSteps['user_table'] = [
            'title_step' => trans('app.Модуль Пользователи: таблицы созданы успешно'),
            'step' => 'install_acl',
            'stop' => false,
            'install' => function ($request) {
                Role::createDbSchema();
                Permission::createDbSchema();
                UserProfile::createDbSchema();
                UserProfileCorporate::createDbSchema();
                UserConfig::createDbSchema();
                sleep(1);
            },
        ];
        $allSteps['install_acl'] = [
            'title_step' => trans('app.Модуль Пользователи: ACL активированы'),
            'step' => 'stop',
            'stop' => false,
            'install' => function ($request) {
                sleep(1);
            },
        ];

        return $allSteps;
    }

    /**
     * Определяет заблокированного пользователя
     *
     * @return bool
     */
    public function isBanned()
    {
        return $this->{self::STATUS} == self::STATUS_BANNED;
    }

    /**
     * Меню администратора
     *
     * Возвращает пунты меню для раздела администратора
     *
     * @return array
     */
    public function getAdminMenuItems()
    {
        $result = [];

        array_push($result, [
            'name' => '<i class="fa fa-table"></i> ' . trans('app.Управление'),
            'route' => '/users/items',
        ]);

        array_push($result, [
            'name' => '<i class="fa fa-table"></i> ' . trans('app.Подписки'),
            'route' => '/users/subscribe',
        ]);

        array_push($result, [
            'name' => '<i class="fa fa-envelope"></i> ' . trans('app.Рассылки'),
            'route' => '/users/mailing',
        ]);

        array_push($result, [
            'name' => '<i class="fa fa-gears"></i> ' . trans('app.Настройки'),
            'route' => '/users/configuration',
        ]);


        return $result;
    }

    /**
     * Возвращает массив таблиц для резервного копирования
     *
     * @return array
     */
    public function getTables()
    {
        $result = [];
        $entityDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'Entity' . DIRECTORY_SEPARATOR;
        $files = \File::files($entityDirectory);
        $directories = \File::directories($entityDirectory);
        foreach ($directories as $directory) {
            array_merge($files, \File::files($directory));
        }

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($files as $file) {

            require_once $file->getPathname();
            $class = '\App\\' . str_replace(['.php'], '', strstr($file->getPathname(), 'Modules'));

            if (class_exists($class)) {
                $class = new $class();
                if (method_exists($class, 'getTable')) {
                    array_push($result, $class->getTable());
                }
            }
        }

        return $result;
    }
}
