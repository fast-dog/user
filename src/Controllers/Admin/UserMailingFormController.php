<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 021 21.09.18
 * Time: 11:29
 */

namespace FastDog\User\Controllers\Admin;

use App\Core\Acl\Role;
use App\Core\Form\Interfaces\FormControllerInterface;
use App\Core\Form\Traits\FormControllerTrait;
use App\Http\Controllers\Controller;
use App\Modules\Config\Entity\DomainManager;
use App\Modules\Config\Entity\Emails;
use FastDog\User\Entity\User;
use FastDog\User\Entity\UserMailing;
use FastDog\User\Entity\UserMailingProcess;
use FastDog\User\Events\UserRegistration;
use FastDog\User\Events\UserUpdate;
use FastDog\User\Jobs\SendMailing;
use FastDog\User\Request\AddMailing;
use FastDog\User\Request\AddUser;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class UserMailingFormController
 * @package FastDog\User\Controllers\Admin
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
        $this->page_title = trans('app.Рассылки');
        parent::__construct();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getEditItem(Request $request): JsonResponse
    {
        $this->breadcrumbs->push(['url' => '/users/mailing', 'name' => trans('app.Управление')]);

        $result = $this->getItemData($request);
        if ($this->item) {
            $this->breadcrumbs->push(['url' => false, 'name' => $this->item->{UserMailing::NAME}]);
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
        }
        /**
         * Создаем процесс отправки
         */
        if ($request->input('create_process', 'N') == 'Y') {
          $process =  UserMailingProcess::create([
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