<?php

namespace FastDog\User\Http\Controllers\Admin;


use FastDog\Core\Form\Interfaces\FormControllerInterface;
use FastDog\Core\Form\Traits\FormControllerTrait;
use FastDog\Core\Http\Controllers\Controller;
use FastDog\User\Jobs\SendMailing;
use FastDog\User\Models\UserMailing;
use FastDog\User\Models\UserMailingProcess;
use FastDog\User\Http\Request\AddMailing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class UserMailingFormController
 * @package FastDog\User\Http\Controllers\Admin
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserMailingFormController extends Controller implements FormControllerInterface
{
    use FormControllerTrait;

    /**
     * UserMailingFormController constructor.
     * @param UserMailing $model
     */
    public function __construct(UserMailing $model)
    {
        $this->model = $model;
        $this->page_title = trans('user::interface.Пользователи') . ' :: ' .
            trans('user::interface.Рассылки');
        parent::__construct();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getEditItem(Request $request): JsonResponse
    {
        $this->breadcrumbs->push(['url' => '/users/items', 'name' => trans('user::interface.Пользователи')]);
        $this->breadcrumbs->push(['url' => '/users/mailing', 'name' => trans('user::interface.Рассылки')]);
        $result = $this->getItemData($request);

        $this->breadcrumbs->push([
            'url' => false,
            'name' => ($this->item->id) ? $this->item->{UserMailing::NAME} : trans('user::forms.mailing.new'),
        ]);

        return $this->json($result, __METHOD__);
    }


    /**
     * Удаление
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postUpdate(Request $request)
    {
        $result = ['success' => true];
        $ids = $request->input('ids');
        if (count($ids)) {
            UserMailing::whereIn('id', $ids)->delete();
        }

        return $this->json($result, __METHOD__);
    }

    /**
     * @param AddMailing $request
     * @return JsonResponse
     */
    public function postMailing(AddMailing $request)
    {
        $result = [
            'success' => true,
            'items' => []
        ];
        $id = $request->input('id');

        $data = [
            UserMailing::NAME => $request->input(UserMailing::NAME),
            UserMailing::SUBJECT => $request->input(UserMailing::SUBJECT),
            UserMailing::TEXT => $request->input(UserMailing::TEXT),
            UserMailing::START_AT => $request->input(UserMailing::START_AT),
            UserMailing::SITE_ID => $request->input(UserMailing::SITE_ID . '.id'),
            UserMailing::TEMPLATE_ID => $request->input(UserMailing::TEMPLATE_ID . '.id'),

        ];
        /**
         * @var $item UserMailing
         */
        $item = UserMailing::where(['id' => $id])->first();

        if ($id > 0) {
            UserMailing::where('id', $id)->update($data);
        } else {
            $data[UserMailing::STATE] = UserMailing::STATE_READY;
            $item = UserMailing::create($data);
            array_push($result['items'], $item->getData());
        }
        /**
         * Создаем процесс отправки
         */
        if ($request->input('create_process', 'N') == 'Y') {
            $process = UserMailingProcess::create([
                UserMailingProcess::MAILING_ID => $item->id,
                UserMailingProcess::CURRENT_STEP => 0,
                UserMailingProcess::STATE => UserMailingProcess::STATE_READY,
            ]);
            $this->dispatch(new SendMailing($process));
        }

        // todo: добавить обработку событий

        return $this->json($result);
    }
}
