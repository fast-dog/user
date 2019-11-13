<?php

namespace FastDog\User;

use FastDog\Config\Models\Translate;
use FastDog\Core\Interfaces\MenuInterface;
use FastDog\Core\Models\BaseModel;
use FastDog\Core\Models\Cache;
use FastDog\Core\Models\Components;
use FastDog\Core\Models\DomainManager;
use FastDog\Menu\Menu;
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
     * @const string
     */
    const MODULE_ID = 'user';
    
    /**
     * Маршрут: авторизация
     * @const string
     */
    const TYPE_LOGIN = 'user_login';
    
    /**
     * Маршрут: выход
     * @const string
     */
    const TYPE_LOGOUT = 'user_logout';
    
    /**
     * Маршрут: регистрация
     * @const string
     */
    const TYPE_REGISTRATION = 'user_registration';
    
    /**
     * Маршрут: восстановление доступа
     * @const string
     */
    const TYPE_RESTORE_PASSWORD = 'user_restore_password';
    
    /**
     * Маршрут: личный кабинет
     * @const string
     */
    const TYPE_CABINET = 'user_cabinet';
    
    /**
     * Маршрут: личный кабинет (редактирование данных профиля)
     * @const string
     */
    const TYPE_CABINET_EDIT = 'user_cabinet_edit';
    
    /**
     * Маршрут: личный кабинет - настройки
     * @const string
     */
    const TYPE_CABINET_SETTINGS = 'user_cabinet_settings';
    
    /**
     * Маршрут: личный кабинет - сообщения
     * @const string
     */
    const TYPE_CABINET_MESSAGES = 'user_cabinet_messages';
    
    /**
     * Маршрут: личный кабинет - новое сообщение
     * @const string
     */
    const TYPE_CABINET_NEW_MESSAGES = 'user_cabinet_new_messages';
    
    /**
     * @const string
     */
    const SETTINGS = 'settings';
    
    /**
     * Параметры конфигурации описанные в module.json
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
            $currentPath = str_replace('{SITE_ID}', $code, $paths);
            
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
                        $name = Arr::last(explode('.', $templateName));
                        
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
            ['id' => 'user_login', 'name' => trans('user::menu.authorization'), 'sort' => 400],
            ['id' => 'user_registration', 'name' => trans('user::menu.registration'), 'sort' => 410],
            ['id' => 'user_restore_password', 'name' => trans('user::menu.password_restore'), 'sort' => 420],
            ['id' => 'user_cabinet', 'name' => trans('user::menu.lk'), 'sort' => 430],
            ['id' => 'user_cabinet_edit', 'name' => trans('user::menu.lk_edit'), 'sort' => 440],
            ['id' => 'user_cabinet_settings', 'name' => trans('user::menu.lk_setting'), 'sort' => 450],
            ['id' => 'user_cabinet_messages', 'name' => trans('user::menu.lk_messages'), 'sort' => 460],
            ['id' => 'user_cabinet_new_messages', 'name' => trans('user::menu.lk_new_message'), 'sort' => 470],
            ['id' => 'user_cabinet_favorites', 'name' => trans('user::menu.lk_favorites'), 'sort' => 480],
            ['id' => 'user_cabinet_billing', 'name' => trans('user::menu.lk_billing'), 'sort' => 490],
            ['id' => 'user_login', 'name' => trans('user::menu.login'), 'sort' => 495],
            ['id' => 'user_logout', 'name' => trans('user::menu.logout'), 'sort' => 500],
        ];
    }
    
    /**
     * @return array
     */
    public function getTemplatesPaths(): array
    {
        return [
            "user_login" => "/vendor/fast_dog/{SITE_ID}/user/login/*.blade.php",
            "user_registration" => "/vendor/fast_dog/{SITE_ID}/user/registration/*.blade.php",
            "user_restore_password" => "/vendor/fast_dog/{SITE_ID}/user/restore_password/*.blade.php",
            "user_cabinet" => "/vendor/fast_dog/{SITE_ID}/user/cabinet/*.blade.php",
            "user_cabinet_edit" => "/vendor/fast_dog/{SITE_ID}/user/cabinet/*.blade.php",
            "user_cabinet_settings" => "/vendor/fast_dog/{SITE_ID}/user/cabinet/settings/*.blade.php",
            "user_cabinet_messages" => "/vendor/fast_dog/{SITE_ID}/user/cabinet/messages/*.blade.php",
            "user_cabinet_new_messages" => "/vendor/fast_dog/{SITE_ID}/user/cabinet/messages/*.blade.php",
            "user_cabinet_my_items" => "/vendor/fast_dog/{SITE_ID}/user/cabinet/items/*.blade.php",
            "user_cabinet_my_buying" => "/vendor/fast_dog/{SITE_ID}/user/cabinet/buying/*.blade.php",
            "user_cabinet_favorites" => "/vendor/fast_dog/{SITE_ID}/user/cabinet/favorites/*.blade.php",
            "user_cabinet_billing" => "/vendor/fast_dog/{SITE_ID}/user/cabinet/billing/*.blade.php",
            "user_cabinet_choose_buyer" => "/vendor/fast_dog/{SITE_ID}/user/cabinet/*.blade.php",
            "user_cabinet_add_opinion" => "/vendor/fast_dog/{SITE_ID}/user/cabinet/reviews/*.blade.php",
            "user_cabinet_add_opinion_buyer" => "/vendor/fast_dog/{SITE_ID}/user/cabinet/reviews/*.blade.php",
        ];
    }
    
    /**
     * Возвращает информацию о модуле
     *
     * @return array
     */
    public function getModuleInfo(): array
    {
        $paths = Arr::first(config('view.paths'));
        $templates_paths = $this->getTemplatesPaths();
        
        return [
            'id' => self::MODULE_ID,
            'menu' => function () use ($paths, $templates_paths) {
                $result = collect();
                foreach ($this->getMenuType() as $id => $item) {
                    $result->push([
                        'id' => self::MODULE_ID . '::' . $item['id'],
                        'name' => $item['name'],
                        'sort' => $item['sort'],
                        'templates' => (isset($templates_paths[$item['id']])) ? $this->getTemplates($paths . $templates_paths[$item['id']]) : [],
                        'class' => __CLASS__,
                    ]);
                }
                $result = $result->sortBy('sort');
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
            'route' => function (Request $request, $item) {
                return $this->getMenuRoute($request, $item);
            }
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
        
        $result = [];
        
        return $result;
    }
    
    
    /**
     * Возвращает маршрут компонента
     *
     * @param  Request  $request
     * @param  MenuInterface|Menu  $item
     * @return mixed
     */
    public function getMenuRoute(Request $request, MenuInterface $item): array
    {
        $result = [];
        $type = $request->input('type.id');
        
        switch ($type) {
            case self::MODULE_ID . '::' . self::TYPE_LOGIN:
                array_push($result, 'login');
                break;
            case self::MODULE_ID . '::' . self::TYPE_LOGOUT:
                array_push($result, 'logout');
                break;
            case self::MODULE_ID . '::' . self::TYPE_CABINET:
                array_push($result, $request->input(BaseModel::ALIAS));
                
                return [
                    'type' => $type,
                    'instance' => CabinetController::class,
                    'route' => implode('/', $result),
                ];
            case self::MODULE_ID . '::' . self::TYPE_CABINET_SETTINGS:
                
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
                    'type' => $type,
                    'instance' => CabinetController::class,
                    'route' => implode('/', $result),
                ];
            case self::MODULE_ID . '::' . self::TYPE_CABINET_MESSAGES:
                if ($item->parent) {
                    array_push($result, $item->parent->alias);
                }
                
                if ($request->input(Menu::ALIAS, null)) {
                    array_push($result, $request->input(Menu::ALIAS, null));
                } else {
                    array_push($result, 'messages');
                }
                
                return [
                    'type' => $type,
                    'instance' => CabinetController::class,
                    'route' => implode('/', $result),
                ];
            case self::MODULE_ID . '::' . self::TYPE_CABINET_NEW_MESSAGES:
                if ($item->parent) {
                    array_push($result, $item->parent->alias);
                }
                if ($request->input(Menu::ALIAS, null)) {
                    array_push($result, $request->input(Menu::ALIAS, null));
                } else {
                    array_push($result, 'new-messages');
                }
                
                return [
                    'type' => $type,
                    'instance' => CabinetController::class,
                    'route' => implode('/', $result),
                ];
            case self::MODULE_ID . '::' . self::TYPE_REGISTRATION:
                array_push($result, 'registration');
                break;
            case self::MODULE_ID . '::' . self::TYPE_RESTORE_PASSWORD:
                array_push($result, 'restore-password');
                break;
            default:
                if ($item->parent) {
                    array_push($result, $item->parent->alias);
                }
                if ($request->input(BaseModel::ALIAS, null)) {
                    array_push($result, $request->input(BaseModel::ALIAS, null));
                } else {
                    array_push($result, $item->alias);
                }
                
                return [
                    'type' => $type,
                    'instance' => CabinetController::class,
                    'route' => implode('/', $result),
                ];
        }
        
        return [
            'type' => ($type) ? $type : 'undefined',
            'instance' => UserController::class,
            'route' => implode('/', $result),
        ];
        
    }
    
    
    /**
     * Метод возвращает отображаемый в публичной части контнет
     *
     * @param  Components  $module
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
     * Параметры публичного раздела
     *
     * Возвращает параметры публичного раздела
     *
     * @return UserConfig
     */
    public function getPublicConfig()
    {
        
        return app()->make(Cache::class)
            ->get(__METHOD__ . '::' . DomainManager::getSiteId() . '::module-users-public', function () {
                
                return UserConfig::where(UserConfig::ALIAS, UserConfig::CONFIG_PUBLIC)->first();
                
            }, ['config']);
        
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
            'new' => '/users/item/0'
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
            'new' => '/users/mailing/0'
        ]);
        
        array_push($result['children'], [
            'icon' => 'fa-gears',
            'name' => trans('user::interface.Настройки'),
            'route' => '/users/configuration',
        ]);

//        array_push($result['children'], [
//            'icon' => 'fa-info',
//            'name' => trans('user::interface.Информация'),
//            'route' => '/users',
//        ]);
        
        return $result;
    }
}
