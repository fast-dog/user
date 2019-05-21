<?php

namespace FastDog\User\Http\Controllers\Admin;




use FastDog\Core\Http\Controllers\Controller;
use FastDog\Core\Models\DomainManager;
use FastDog\Core\Models\ModuleManager;
use FastDog\User\Models\UserConfig;
use FastDog\User\Models\UserMailingProcess;
use FastDog\User\Models\UserMailingTemplates;
use FastDog\User\Models\UserRegisterStatistic;
use FastDog\User\Models\UserVisitStatistic;
use FastDog\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ApiController extends Controller
{
    /**
     * ApiController constructor.
     */
    public function __construct()
    {
        $this->page_title = trans('app.Пользователи');
    }

    /**
     * Список доступных типов учентых записей
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getTypeList()
    {
        $result = ['success' => true, 'items' => [
            [
                'id' => User::USER_TYPE_USER,
                'name' => trans('app.Пользователь'),
            ],
            [
                'id' => User::USER_TYPE_ADMIN,
                'name' => trans('app.Администратор'),
            ],
        ]];

        return $this->json($result);
    }

    /**
     * Информация по модулю
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAdminInfo(Request $request)
    {
        $result = ['success' => true,
            'items' => [],
            'page_title' => trans('app.Пользователи'),
            'breadcrumbs' => [
                ['url' => '/', 'name' => trans('app.Главная')],
                ['url' => false, 'name' => trans('app.Настройки')],
            ],
        ];
        $moduleManager = \App::make(ModuleManager::class);
        /**
         * @var $moduleManager ModuleManager
         */
        $module = $moduleManager->getInstance('FastDog\User\User');

        /**
         * Параметры модуля  idx => 0
         */
        array_push($result['items'], (array)$module->getConfig());

        /**
         * Статистика базы данных пользователей учитывая доменное разделение idx => 1
         */
        array_push($result['items'], User::getStatistic(false));

        /**
         * Параметры модуля
         */
        array_push($result['items'], (array)UserConfig::getAllConfig());

        array_push($result['items'], (array)UserVisitStatistic::getStatistic());

        array_push($result['items'], (array)UserRegisterStatistic::getStatistic());


        return $this->json($result, __METHOD__);
    }

    /**
     * Сохранение параметров модуля
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function postSaveModuleConfigurations(Request $request)
    {
        $result = ['success' => true, 'items' => []];

        $type = $request->input('type');
        $item = UserConfig::where(UserConfig::ALIAS, $type)->first();
        if ($item) {
            $values = $request->input('value');
            switch ($type) {
                case UserConfig::CONFIG_DESKTOP:
                    foreach ($values as $value) {
                        Desktop::check($value['value'], [
                            'name' => $value['name'],
                            'type' => $value['type'],
                            'data' => [
                                'data' => $value['data'],
                                'cols' => 3,
                            ],
                        ]);
                    }
                    break;
                case UserConfig::CONFIG_PUBLIC:
                    break;
                default:
                    break;
            }
            UserConfig::where('id', $item->id)->update([
                UserConfig::VALUE => json_encode($values),
            ]);
        }

        array_push($result['items'], UserConfig::getAllConfig());

        return $this->json($result, __METHOD__);
    }

    /**
     * Изменение доступа к модулю
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postAccess(Request $request)
    {
        $result = ['success' => false];

        $role = Role::where([
            Role::NAME => $request->input('role'),
        ])->first();
        if ($role) {
            $permission = Permission::where(function (Builder $query) use ($request, $role) {
                $query->where(Permission::NAME, $request->input('permission') . '::' . $role->slug);
            })->first();

            if ($permission) {
                if (isset($permission->slug[$request->input('accessName')])) {
                    $permission_slug = $permission->slug;
                    $permission_slug[$request->input('accessName')] = ($request->input('accessValue') == 'Y') ? true : false;
                    Permission::where('id', $permission->id)->update([
                        'slug' => \GuzzleHttp\json_encode($permission_slug),
                    ]);
                }
            }
            $result['acl'] = Config::getAcl(DomainManager::getSiteId(), strtolower(User::class));
        }

        return $this->json($result, __METHOD__);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getMailingProcess(Request $request): JsonResponse
    {
        $result = ['success' => true, 'items' => []];

        /** @var Collection $items */
        $items = UserMailingProcess::orderBy('id', 'desc')->paginate(25);

        $items->each(function (UserMailingProcess $process) use (&$result) {


            array_push($result['items'], $process->getData());
        });

        $this->_getCurrentPaginationInfo($request, $items, $result);

        return $this->json($result, __METHOD__);
    }
}