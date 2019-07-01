<?php

namespace FastDog\User;

use App\Core\Module\Components;
use FastDog\Config\Models\Translate;
use FastDog\Core\Interfaces\MenuInterface;
use FastDog\Core\Models\DomainManager;
use FastDog\User\Http\Controllers\Site\CabinetController;
use FastDog\User\Http\Controllers\Site\UserController;
use FastDog\User\Models\MessageManager;
use FastDog\User\Models\User as UserModel;

use FastDog\User\Models\UserConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

/**
 * Пользователи
 *
 * @package FastDog\User
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class User extends UserModel
{

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
    public function getMenuType(): array
    {
        return [
            ['id' => 'user_login', 'name' => 'Пользователи :: Авторизация', 'sort' => 400],
            ['id' => 'user_registration', 'name' => 'Пользователи :: Регистрация', 'sort' => 410],
            ['id' => 'user_restore_password', 'name' => 'Пользователи :: Восстановление пароля', 'sort' => 420],
            ['id' => 'user_cabinet', 'name' => 'Пользователи :: Личный кабинет', 'sort' => 430],
            ['id' => 'user_cabinet_edit', 'name' => 'Пользователи :: Личный кабинет (редактирование)', 'sort' => 440],
            ['id' => 'user_cabinet_settings', 'name' => 'Пользователи :: Личный кабинет - настройки', 'sort' => 450],
            ['id' => 'user_cabinet_messages', 'name' => 'Пользователи :: Личный кабинет - сообщения', 'sort' => 460],
            ['id' => 'user_cabinet_new_messages', 'name' => 'Пользователи :: Личный кабинет - новое сообщение', 'sort' => 470],
            ['id' => 'user_cabinet_favorites', 'name' => 'Пользователи :: Личный кабинет - закладки', 'sort' => 480],
            ['id' => 'user_cabinet_billing', 'name' => 'Пользователи :: Личный кабинет - счет', 'sort' => 490],
            ['id' => 'user_logout', 'name' => 'Пользователи :: Выход', 'sort' => 500],
        ];
    }

    /**
     * @return array
     */
    public function getTemplatesPaths(): array
    {
        return [
            "user_login" => "/users/login/*.blade.php",
            "user_registration" => "/users/registration/*.blade.php",
            "user_restore_password" => "/users/restore_password/*.blade.php",
            "user_cabinet" => "/users/cabinet/*.blade.php",
            "user_cabinet_edit" => "/users/cabinet/*.blade.php",
            "user_cabinet_settings" => "/users/cabinet/settings/*.blade.php",
            "user_cabinet_messages" => "/users/cabinet/messages/*.blade.php",
            "user_cabinet_new_messages" => "/users/cabinet/messages/*.blade.php",
            "user_cabinet_my_items" => "/users/cabinet/items/*.blade.php",
            "user_cabinet_my_buying" => "/users/cabinet/buying/*.blade.php",
            "user_cabinet_favorites" => "/users/cabinet/favorites/*.blade.php",
            "user_cabinet_billing" => "/users/cabinet/billing/*.blade.php",
            "user_cabinet_choose_buyer" => "/users/cabinet/*.blade.php",
            "user_cabinet_add_opinion" => "/users/cabinet/reviews/*.blade.php",
            "user_cabinet_add_opinion_buyer" => "/users/cabinet/reviews/*.blade.php",
        ];
    }

    /**
     * Возвращает информацию о модуле
     *
     * @return array
     */
    public function getModuleInfo(): array
    {
        $paths = Arr::first(\Config::get('view.paths'));
        $templates_paths = $this->getTemplatesPaths();

        return [
            'id' => self::MODULE_ID,
            'menu' => function () use ($paths, $templates_paths) {
                $result = [];
                foreach ($this->getMenuType() as $id => $item) {
                    array_push($result, [
                        'id' => $id,
                        'name' => $item,
                        'templates' => (isset($templates_paths[$id])) ? $this->getTemplates($paths . $templates_paths[$id]) : [],
                        'class' => __CLASS__,
                    ]);
                }

                return $result;
            },
            'templates_paths' => $templates_paths,
            'module_type' => $this->getMenuType(),
            'admin_menu' => function () {
                return $this->getAdminMenuItems();
            },
            'access' => function () {
                return [
                    '000',
                ];
            },
        ];
    }

    /**
     * Устанавливает параметры в контексте объекта
     *
     * @param $data \StdClass
     * @return mixed
     */
    public function setConfig(\StdClass $data): void
    {
        $this->data = $data;
    }

    /**
     *  Возвращает параметры объекта
     *
     * @return mixed
     */
    public function getConfig(): \StdClass
    {
        return $this->data;
    }


    /**
     * Возвращает возможные типы модулей
     *
     * @return mixed
     */
    public function getModuleType(): array
    {
        $paths = Arr::first(\Config::get('view.paths'));

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
     * @param MenuInterface $item
     * @return mixed
     */
    public function getMenuRoute(Request $request, MenuInterface $item): array
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
     * Метод возвращает отображаемый в публичной части контнет
     *
     * @param Components $module
     * @return null|string
     * @throws \Throwable
     */
    public function getContent(Components $module): string
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
    public function getModuleDir(): string
    {
        return dirname(__FILE__);
    }

    /**
     * Возвращает параметры блоков добавляемых на рабочий стол администратора
     *
     * @return array
     */
    public function getDesktopWidget(): array
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
     * Меню администратора
     *
     * Возвращает пунты меню для раздела администратора
     *
     * @return array
     */
    public function getAdminMenuItems()
    {
        $result = [
            'icon' => 'fa-users',
            'name' => trans('user::interface.Пользователи'),
            'route' => '/users',
            'children' => [],
        ];

        array_push($result['children'], [
            'icon' => 'fa-table',
            'name' => trans('user::interface.Управление'),
            'route' => '/users/items',
        ]);

        array_push($result['children'], [
            'icon' => 'fa-table',
            'name' => trans('user::interface.Подписки'),
            'route' => '/users/subscribe',
        ]);

        array_push($result['children'], [
            'icon' => 'fa-envelope',
            'name' => trans('user::interface.Рассылки'),
            'route' => '/users/mailing',
        ]);

        array_push($result['children'], [
            'icon' => 'fa-gears',
            'name' => trans('user::interface.Настройки'),
            'route' => '/users/configuration',
        ]);

        array_push($result['children'], [
            'icon' => 'fa-info',
            'name' => trans('user::interface.Информация'),
            'route' => '/users',
        ]);

        return $result;
    }
}