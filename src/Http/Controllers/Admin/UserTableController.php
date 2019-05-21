<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 28.05.2018
 * Time: 7:40
 */

namespace FastDog\User\Http\Controllers\Admin;


use FastDog\Core\Http\Controllers\Controller;
use FastDog\Core\Table\Interfaces\TableControllerInterface;
use FastDog\Core\Table\Traits\TableTrait;
use FastDog\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Пользователи - таблица
 *
 * @package FastDog\User\Http\Controllers\Admin
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserTableController extends Controller implements TableControllerInterface
{
    use  TableTrait;

    /**
     * Имя  списка доступа
     * @var string $accessKey
     */
    protected $accessKey = '';

    /**
     * Модель по которой будет осуществляться выборка данных
     *
     * @var \FastDog\User\User|null $model
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
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct();
        $this->model = $model;
        $this->accessKey = $this->model->getAccessKey();
        $this->initTable();
        $this->page_title = trans('app.Пользователи');
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
        $this->breadcrumbs->push(['url' => false, 'name' => trans('app.Управление')]);

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
     * Поля для выборки по умолчанию
     *
     * @return array
     */
    public function getDefaultSelectFields(): array
    {
        return [BaseModel::DELETED_AT];
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function items(Request $request): JsonResponse
    {
        // TODO: Implement items() method.
    }
}