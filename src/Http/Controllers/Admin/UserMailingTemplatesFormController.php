<?php

namespace FastDog\User\Http\Controllers\Admin;

use FastDog\Core\Form\Interfaces\FormControllerInterface;
use FastDog\Core\Form\Traits\FormControllerTrait;
use FastDog\Core\Http\Controllers\Controller;
use FastDog\User\Models\UserMailing;
use FastDog\User\Models\UserMailingTemplates;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class UserMailingFormController
 * @package FastDog\User\Http\Controllers\Admin
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserMailingTemplatesFormController extends Controller implements FormControllerInterface
{
    use FormControllerTrait;

    /**
     * UserMailingFormController constructor.
     * @param UserMailingTemplates $model
     */
    public function __construct(UserMailingTemplates $model)
    {
        $this->model = $model;
        $this->page_title = trans('user::interface.Пользователи') . ' :: ' .
            trans('user::interface.Рассылки') . ' :: ' . trans('user::interface.Шаблоны рассылок');
        parent::__construct();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getEditItem(Request $request): JsonResponse
    {
        $this->breadcrumbs->push(['url' => '/users/mailing', 'name' => trans('user::interface.Рассылки')]);
        $this->breadcrumbs->push(['url' => '/users/mailing-templates', 'name' => trans('user::interface.Шаблоны рассылок')]);

        $result = $this->getItemData($request);

        $this->breadcrumbs->push([
            'url' => false,
            'name' => ($this->item->id) ? $this->item->{UserMailingTemplates::NAME} : trans('user::forms.templates.new'),
        ]);


        return $this->json($result, __METHOD__);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postMailingTemplate(Request $request)
    {
        $result = [
            'success' => true,
        ];
        $id = $request->input('id');

        $data = [
            UserMailingTemplates::NAME => $request->input(UserMailingTemplates::NAME),
            UserMailingTemplates::TEXT => $request->input(UserMailingTemplates::TEXT),
        ];
        /**
         * @var $item UserMailing
         */
        $item = UserMailingTemplates::where(['id' => $id])->first();

        if ($id > 0) {
            UserMailingTemplates::where('id', $id)->update($data);
        } else {
            $data[UserMailingTemplates::STATE] = UserMailingTemplates::STATE_PUBLISHED;
            $item = UserMailingTemplates::create($data);

            $request->merge(['id' => $item->id]);

            return $this->getEditItem($request);
        }

        $item->storeProperties(collect($request->input('properties', [])));

        return $this->json($result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postMailingTemplateUpdate(Request $request): JsonResponse
    {
        $result = ['success' => true];
        $ids = $request->input('ids');
        if (count($ids)) {
            UserMailingTemplates::whereIn('id', $ids)->delete();
        }

        return $this->json($result, __METHOD__);
    }
}