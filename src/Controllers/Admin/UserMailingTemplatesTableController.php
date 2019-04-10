<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 006 06.10.18
 * Time: 14:03
 */

namespace FastDog\User\Controllers\Admin;


use App\Core\Table\Interfaces\TableControllerInterface;
use App\Core\Table\Traits\TableTrait;
use App\Http\Controllers\Controller;
use FastDog\User\Entity\UserMailingTemplates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class UserMailingTemplatesTableController
 *
 * @package FastDog\User\Controllers\Admin
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserMailingTemplatesTableController extends Controller implements TableControllerInterface
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
     * @var \FastDog\User\Entity\UserMailingTemplates|null $model
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
        $this->accessKey = $this->model->getAccessKey();
        $this->initTable();
        $this->page_title = trans('app.Рассылки') . ' / ' . trans('app.Шаблоны');
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