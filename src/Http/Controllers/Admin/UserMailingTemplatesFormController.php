<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 021 21.09.18
 * Time: 11:29
 */

namespace FastDog\User\Http\Controllers\Admin;

use App\Core\Form\Interfaces\FormControllerInterface;
use App\Core\Form\Traits\FormControllerTrait;
use App\Http\Controllers\Controller;
use FastDog\User\Entity\UserMailing;
use FastDog\User\Entity\UserMailingTemplates;
use FastDog\User\Request\AddMailing;
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
        $this->page_title = trans('app.Рассылки') . ' / ' . trans('app.Шаблоны');
        parent::__construct();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getEditItem(Request $request): JsonResponse
    {
        $this->breadcrumbs->push(['url' => '/users/mailing', 'name' => trans('app.Управление')]);
        $this->breadcrumbs->push(['url' => '/users/mailing-templates', 'name' => trans('app.Шаблоны')]);

        $result = $this->getItemData($request);
        if ($this->item) {
            $this->breadcrumbs->push(['url' => false, 'name' => $this->item->{UserMailingTemplates::NAME}]);
        }

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
        }

        $item->storeProperties(collect($request->input('properties', [])));

        return $this->json($result);
    }
}