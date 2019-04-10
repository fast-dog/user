<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 021 21.09.18
 * Time: 9:46
 */

namespace FastDog\User\Controllers\Admin;


use App\Core\Table\Interfaces\TableControllerInterface;
use App\Core\Table\Traits\TableTrait;
use App\Http\Controllers\Controller;
use FastDog\User\Entity\UserMailing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class UserMailingTableController
 * @package FastDog\User\Controllers\Admin
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserMailingTableController extends Controller implements TableControllerInterface
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
     * @var \FastDog\User\Entity\UserMailing|null $model
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
     * @param UserMailing $model
     */
    public function __construct(UserMailing $model)
    {
        parent::__construct();
        $this->model = $model;
        $this->accessKey = $this->model->getAccessKey();
        $this->initTable();
        $this->page_title = trans('app.Рассылки');
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

}