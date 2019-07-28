<?php

namespace FastDog\User\Http\Controllers\Admin;


use FastDog\Core\Http\Controllers\Controller;
use FastDog\Core\Table\Interfaces\TableControllerInterface;
use FastDog\Core\Table\Traits\TableTrait;
use FastDog\User\Models\UserMailing;
use FastDog\User\Models\UserMailingTemplates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class UserMailingTemplatesTableController
 *
 * @package FastDog\User\Http\Controllers\Admin
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserMailingTemplatesTableController extends Controller implements TableControllerInterface
{
    use  TableTrait;

    /**
     * Модель по которой будет осуществляться выборка данных
     *
     * @var \FastDog\User\Models\UserMailingTemplates|null $model
     */
    protected $model = null;

    /**
     * Модель, контекст выборок
     *
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * ContentController constructor.
     * @param UserMailingTemplates $model
     */
    public function __construct(UserMailingTemplates $model)
    {
        parent::__construct();
        $this->model = $model;
        $this->initTable();
        $this->page_title = trans('user::interface.Пользователи') . ' :: ' .
            trans('user::interface.Рассылки') . ' :: ' . trans('user::interface.Шаблоны рассылок');
    }


    /**
     * Таблица - Материалы
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $result = self::paginate($request);
        $this->breadcrumbs->push(['url' => '/users/items', 'name' => trans('user::interface.Пользователи')]);
        $this->breadcrumbs->push(['url' => '/users/mailing', 'name' => trans('user::interface.Рассылки')]);
        $this->breadcrumbs->push(['url' => false, 'name' => trans('user::interface.Шаблоны рассылок')]);

        return $this->json($result, __METHOD__);
    }

    /**
     * Описание структуры колонок таблицы
     *
     * @return Collection
     */
    public function getCols(): Collection
    {
        return $this->table->getCols();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function items(Request $request): JsonResponse
    {
        // TODO: Implement items() method.
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function postUserMailingUpdate(Request $request): JsonResponse
    {
        $result = ['success' => false, 'items' => []];

        try {
            $result['success'] = $this->updatedModel($request->all(), UserMailing::class);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], __METHOD__);
        }

        return $this->json($result, __METHOD__);
    }
}
