<?php

namespace FastDog\User\Http\Controllers\Admin;


use FastDog\Config\Models\Emails;
use FastDog\Core\Form\Interfaces\FormControllerInterface;
use FastDog\Core\Form\Traits\FormControllerTrait;
use FastDog\Core\Http\Controllers\Controller;
use FastDog\Core\Models\DomainManager;
use FastDog\User\Events\UserRegistration;
use FastDog\User\Events\UserUpdate;
use FastDog\User\Models\User;
use FastDog\User\Request\AddUser;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Пользователи - форма
 *
 * @package FastDog\User\Http\Controllers\Admin
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserFormController extends Controller implements FormControllerInterface
{
    use FormControllerTrait;

    public function __construct(User $model)
    {
        $this->model = $model;
        $this->page_title = trans('user::interface.Пользователи');
        parent::__construct();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getEditItem(Request $request): JsonResponse
    {
        $this->breadcrumbs->push(['url' => '/users/items', 'name' => trans('user::interface.Управление')]);

        $result = $this->getItemData($request);
        if ($this->item) {
            $this->breadcrumbs->push(['url' => false, 'name' => $this->item->{User::EMAIL}]);
        }

        return $this->json($result, __METHOD__);
    }

    /**
     * Удалние пользователей
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function postUserDelete(Request $request)
    {
        $result = ['success' => true];
        $ids = $request->input('ids');
        if (count($ids)) {
            User::whereIn('id', $ids)->delete();
        }

        return $this->json($result, __METHOD__);
    }

    /**
     * @param AddUser $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postUser(AddUser $request)
    {
        $result = [
            'success' => true,
        ];
        if ($request->has('ids')) {
            return $this->postUserDelete($request);
        }

        $id = $request->input('id');
        /** @var User $user */
        $user = User::find($id);

        $data = [
            User::EMAIL => $request->input(User::EMAIL),
            User::TYPE => $request->input(User::TYPE . '.id'),
            User::STATUS => $request->input(User::STATUS . '.id'),
        ];

        if ($request->has(User::PASSWORD) && ($request->input(User::PASSWORD) !== '')) {
            $data[User::PASSWORD] = \Hash::make($request->input(User::PASSWORD));
        }

        if (DomainManager::checkIsDefault()) {
            $data[User::SITE_ID] = $request->input(User::SITE_ID . '.id');
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
        \Event::fire(new UserUpdate($user, $request));

        return $this->json($result, __METHOD__);
    }

    /**
     * @param User $item
     */
    public function setItem(User $item): void
    {
        $this->item = $item;
    }
}