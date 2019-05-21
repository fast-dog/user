<?php

namespace FastDog\User\Http\Controllers\Admin;

use FastDog\Admin\Http\Controllers\AdminController;
use FastDog\Core\Models\DomainManager;
use FastDog\User\Entity\MessageManager;
use FastDog\User\Events\UserAdminPrepare;
use FastDog\User\Events\UserRegistration;
use FastDog\User\Events\UserUpdate;
use FastDog\User\Request\AddUser;
use FastDog\User\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Администрирование
 *
 * @package FastDog\User\Http\Controllers\Admin
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 * @deprecated
 */
class UserController extends AdminController
{

    /**
     * Имя родительского списка доступа
     *
     * из за реализации ACL в пакете kodeine/laravel-acl
     * нужно использовать имя верхнего уровня: action.__CLASS__::SITE_ID::access_level
     *
     * @var string $accessKey
     */
    protected $accessKey = '';

    /**
     * ContentController constructor.
     */
    public function __construct()
    {
        $this->accessKey = strtolower(User::class) . '::' . DomainManager::getSiteId() . '::guest';
        parent::__construct();
    }


    /**
     * Получение списка учетных записей
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postList(Request $request)
    {
        /**
         * @var $user User
         */
        $user = \Auth::getUser();

        $result = [
            'page_title' => trans('app.Пользователи'),
            'breadcrumbs' => [
                ['url' => '/', 'name' => trans('app.Главная')],
                ['url' => false, 'name' => trans('app.Управление')],
            ],
            'access' => [
                'reorder' => $user->can('reorder.' . $this->accessKey),
                'delete' => $user->can('delete.' . $this->accessKey),
                'update' => $user->can('update.' . $this->accessKey),
                'create' => $user->can('create.' . $this->accessKey),
            ],
            'cols' => [
                [
                    'name' => 'Email',
                    'key' => User::EMAIL,
                    'domain' => true,
                    'link' => 'user_profile',
                ],
                [
                    'name' => trans('app.Дата регистрации'),
                    'key' => 'created_at',
                    'width' => 150,
                    'link' => null,
                    'class' => 'text-center',
                ],
//                [
//                    'name' => trans('project.Сообщений'),
//                    'key' => 'count_messages',
//                    'link' => null,
//                    'width' => 80,
//                    'class' => 'text-center',
//                ],
//                [
//                    'name' => trans('project.Рейтинг'),
//                    'key' => 'rating',
//                    'link' => null,
//                    'width' => 80,
//                    'class' => 'text-center',
//                ],
                [
                    'name' => '#',
                    'key' => 'id',
                    'link' => null,
                    'width' => 80,
                    'class' => 'text-center',
                ],
            ],
            'success' => true,
            'items' => [],
            'status' => User::getStatusList(),
        ];

        $sort = (isset($request->order_by)) ? $request->order_by : 'id';
        $direction = (isset($request->direction)) ? $request->direction : 'desc';

        $scope = '';
        $items = User::where(function ($query) use ($request, &$scope) {
            if (!DomainManager::checkIsDefault()) {
                $query->where(User::SITE_ID, DomainManager::getSiteId());
            }
            $this->_getMenuFilter($query, $request, $scope, User::class);
        })->orderBy($sort, $direction)->paginate(25);

        /**
         * @var $messageManager MessageManager
         */
        $messageManager = \App::make(MessageManager::class);


        /**
         * @var $item User
         */
        foreach ($items as $item) {
            $unread = $messageManager->getUnreadCountByUserId($item->id);
            $total = $messageManager->getCountByUserId($item->id);
            $data = [
                'id' => $item->id,
                'email' => $item->email,
                'orders' => $item->orders,
                'created_at' => ($item->created_at) ? $item->created_at->format('d.m.y') : '',
                'checked' => false,
                User::SITE_ID => $item->{User::SITE_ID},
                'rating' => ($item->rating) ? $item->rating->getRating() : 0,
                'count_messages' => $total . "\<strong>" . $unread . "</strong>",
            ];
            if (DomainManager::checkIsDefault()) {
                $data['suffix'] = DomainManager::getDomainSuffix($item[User::SITE_ID]);
            }
            array_push($result['items'], $data);
        }

        $this->_getCurrentPaginationInfo($request, $items, $result);

        return $this->json($result);
    }

    /**
     * Получение данных учетной записи
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser(Request $request)
    {
        /**
         * @var $user User
         */
        $user = \Auth::getUser();

        $result = [
            'page_title' => trans('app.Пользователи'),
            'breadcrumbs' => [
                ['url' => '/', 'name' => trans('app.Главная')],
                ['url' => '/users/items', 'name' => trans('app.Управление')],
            ],
            'access' => [
                'reorder' => $user->can('reorder.' . $this->accessKey),
                'delete' => $user->can('delete.' . $this->accessKey),
                'update' => $user->can('update.' . $this->accessKey),
                'create' => $user->can('create.' . $this->accessKey),
            ],
            'type' => User::getAllType(),
            'type_corporate' => User::getAllTypeCorporate(),
            'countries' => User::getAllCountry(),
            'allow_access' => DomainManager::checkIsDefault(),
            'success' => true,
            'status' => User::getStatusList(),
            'items' => [],

        ];
        if (DomainManager::checkIsDefault()) {
            $result['access_list'] = (array)DomainManager::getAccessDomainList();
        }
        $editId = \Route::input('id', null);
        if ($editId !== 'new') {
            /**
             * @var  $user User
             */
            $user = User::where('id', $editId)->first();

        } else if ($editId === 'new') {
            $user = new User();
            $user->type = User::USER_TYPE_USER;
        }

        if ($user) {
            $data = $user->getData();
            $data['allow_acl'] = $user->getAclRoles();
            array_push($result['breadcrumbs'], ['url' => false, 'name' => $data[User::EMAIL]]);
            if (isset($data['profile']['birth'])) {
                if ($data['profile']['birth'] instanceof Carbon) {
                    $data['profile']['birth'] = $data['profile']['birth']->format('Y-m-d');
                }
            }
            array_push($result['items'], $data);

            \Event::fire(new UserAdminPrepare($result, $user));
        }

        return $this->json($result);
    }

    /**
     * Удалние пользователей
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postUserDelete(Request $request)
    {
        $result = ['success' => true];
        $ids = $request->input('ids');
        if (count($ids)) {
            User::whereIn('id', $ids)->delete();
        }

        return $this->json($result);
    }

    /**
     * @param AddUser $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postUser(AddUser $request)
    {
        $result = [
            'success' => true,
            'page_title' => trans('app.Пользователи'),
            'breadcrumbs' => [
                ['url' => '/', 'name' => trans('app.Главная')],
                ['url' => '/users/items', 'name' => trans('app.Управление')],
            ],
            'items' => [],
        ];

        $id = $request->input('id');
        /**
         * @var $user User
         */
        $user = User::find($id);

        $data = [
            User::EMAIL => $request->input(User::EMAIL),
            User::TYPE => $request->input(User::TYPE),
            User::STATUS => $request->input(User::STATUS),
            User::GROUP_ID => $request->input(User::GROUP_ID),
        ];

        if ($request->has(User::PASSWORD) && ($request->input(User::PASSWORD) !== '')) {
            $data[User::PASSWORD] = \Hash::make($request->input(User::PASSWORD));
        }

        if (DomainManager::checkIsDefault()) {
            $data[User::SITE_ID] = $request->input(User::SITE_ID);
        }
        if ($user && $id > 0) {
            User::where('id', $user->id)->update($data);
            if ($data[User::STATUS] === User::STATUS_BANNED && ($user->{User::STATUS} !== User::STATUS_BANNED)) {
                Emails::send('user_banned', [
                    'user_name' => $user->getName(),
                    'to' => $user->{User::EMAIL},
                    'date' => Carbon::now()->format('Y.m.d H:i'),
                ]);
            }

        } else {
            $data[User::STATUS] = User::STATUS_ACTIVE;
            $user = User::create($data);
            \Event::fire(new UserRegistration($user));
        }
        if ($user->{User::GROUP_ID} <> $data[User::GROUP_ID]) {
            $oldRole = Role::find($user->{User::GROUP_ID});
            if ($oldRole) {
                $user->revokeRole($oldRole);
            }
            $newRole = Role::find($data[User::GROUP_ID]);
            if ($newRole) {
                $user->assignRole($newRole);
            }
        }
        \Event::fire(new UserUpdate($user, $request));

        if ($user) {
            $data = $user->getData();
            $data['allow_acl'] = $user->getAclRoles();
            array_push($result['breadcrumbs'], ['url' => false, 'name' => $data[User::EMAIL]]);
            array_push($result['items'], $data);
        }

        return $this->json($result);
    }


    /**
     * Список событий для начисления рейтинга
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postRating(Request $request)
    {
        /**
         * @var $user User
         */
        $user = \Auth::getUser();

        $result = [
            'page_title' => trans('app.Пользователи'),
            'breadcrumbs' => [
                ['url' => '/', 'name' => trans('app.Главная')],
                ['url' => false, 'name' => trans('project.Рейтинг пользователей')],
            ],
            'access' => [
                'reorder' => $user->can('reorder.' . $this->accessKey),
                'delete' => $user->can('delete.' . $this->accessKey),
                'update' => $user->can('update.' . $this->accessKey),
                'create' => $user->can('create.' . $this->accessKey),
            ],
            'cols' => [
                [
                    'name' => trans('project.Событие'),
                    'key' => RatingEvents::NAME,
                    'link' => 'user_rating_item',
                ],
                [
                    'name' => trans('project.Рейтинг'),
                    'key' => RatingEvents::RATE,
                    'link' => null,
                    'width' => 80,
                    'class' => 'text-center',
                ],
                [
                    'name' => '#',
                    'key' => 'id',
                    'link' => null,
                    'width' => 80,
                    'class' => 'text-center',
                ],
            ],
            'success' => true,
            'items' => [],
        ];

        $items = RatingEvents::where(function ($query) {

        })->paginate(self::PAGE_SIZE);

        /**
         * @var $item RatingEvents
         */
        foreach ($items as $item) {
            $data = [
                'id' => $item->id,
                RatingEvents::NAME => $item->{RatingEvents::NAME},
                RatingEvents::RATE => $item->{RatingEvents::RATE},
            ];
            array_push($result['items'], $data);
        }

        $this->_getCurrentPaginationInfo($request, $items, $result);

        return $this->json($result, __METHOD__);
    }

    /**
     * Редактирование события для начисления рейтинга
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserRating(Request $request)
    {
        /**
         * @var $user User
         */
        $user = \Auth::getUser();

        $result = [
            'page_title' => trans('app.Пользователи'),
            'breadcrumbs' => [
                ['url' => '/', 'name' => trans('app.Главная')],
                ['url' => '/users/rating', 'name' => trans('project.Рейтинг пользователей')],
            ],
            'access' => [
                'reorder' => $user->can('reorder.' . $this->accessKey),
                'delete' => $user->can('delete.' . $this->accessKey),
                'update' => $user->can('update.' . $this->accessKey),
                'create' => $user->can('create.' . $this->accessKey),
            ],
            'allow_access' => DomainManager::checkIsDefault(),
            'success' => true,
            'items' => [],
        ];

        $item = RatingEvents::find(\Route::input('id'));
        if ($item) {
            array_push($result['items'], [
                'id' => $item->id,
                RatingEvents::NAME => $item->{RatingEvents::NAME},
                RatingEvents::RATE => $item->{RatingEvents::RATE},
            ]);
        }

        return $this->json($result, __METHOD__);
    }

    /**
     * Сохранение события для начисления рейтинга
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postRatingSave(Request $request)
    {
        /**
         * @var $user User
         */
        $user = \Auth::getUser();

        $result = [
            'page_title' => trans('app.Пользователи'),
            'breadcrumbs' => [
                ['url' => '/', 'name' => trans('app.Главная')],
                ['url' => '/users/rating', 'name' => trans('project.Рейтинг пользователей')],
            ],
            'access' => [
                'reorder' => $user->can('reorder.' . $this->accessKey),
                'delete' => $user->can('delete.' . $this->accessKey),
                'update' => $user->can('update.' . $this->accessKey),
                'create' => $user->can('create.' . $this->accessKey),
            ],
            'allow_access' => DomainManager::checkIsDefault(),
            'success' => true,
            'items' => [],
        ];
        $item = RatingEvents::find($request->input('id'));
        if ($item) {

            RatingEvents::where('id', $item->id)->update([
                RatingEvents::RATE => $request->input(RatingEvents::RATE),
            ]);

            $item = RatingEvents::find($request->input('id'));
            array_push($result['items'], [
                'id' => $item->id,
                RatingEvents::NAME => $item->{RatingEvents::NAME},
                RatingEvents::RATE => $item->{RatingEvents::RATE},
            ]);
        }

        return $this->json($result, __METHOD__);
    }


    /**
     * Обработка внешнего фильтра модели
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Request $request
     * @param string $scope
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return mixed
     */
    public function _getMenuFilter(&$query, $request, &$scope, $model)
    {
        $filter = $request->input('filter', []);

        foreach ($filter as $name => $value) {
            switch ($name) {
                case 'title':
                    if ($value <> '') {
                        $query->where($model::EMAIL, 'LIKE', '%' . $value . '%');
                    }
                    break;
                case 'date':
                    if ((isset($value['id']) && !empty($value['id'])) && (isset($value['date']) && !empty($value['date']))) {
                        $dates = explode('#', $value['date']);
                        $start = Carbon::createFromFormat('Y-m-d', $dates[0])->startOfDay()->format(Carbon::DEFAULT_TO_STRING_FORMAT);
                        $end = Carbon::createFromFormat('Y-m-d', $dates[1])->startOfDay()->format(Carbon::DEFAULT_TO_STRING_FORMAT);
                        $query->where($value['id'], '>=', $start);
                        $query->where($value['id'], '<=', $end);
                    }
                    break;
                case 'status':
                case 'state':
                    if ($value <> '') {
                        $query->where($model::STATUS, $value);
                    }
                    break;
                case 'site_id':
                    if (DomainManager::checkIsDefault()) {
                        if ($value !== '000') {
                            $scope = 'default';
                            $query->where($model::SITE_ID, $value);
                        }
                    }
                    break;
            }
        }

        $order = $request->input('order');
        if ($order) {
            $order = explode(':', $order);
            if ($order[0] == 'title') {
                $order[0] = $model::NAME;
            }
            if (!in_array($order[0], self::$orderField)) {
                $order[0] = 'id';
            }
            if (!in_array($order[1], self::$orderDirections)) {
                $order[1] = 'asc';
            }

            $request->merge([
                'order_by' => $order[0],
                'direction' => $order[1],
            ]);
        }

        return $query;
    }

}
