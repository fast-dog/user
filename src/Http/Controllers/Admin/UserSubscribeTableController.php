<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 16.09.2018
 * Time: 17:25
 */

namespace FastDog\User\Http\Controllers\Admin;


use App\Core\BaseModel;
use App\Core\Table\Interfaces\TableControllerInterface;
use App\Core\Table\Traits\TableTrait;
use App\Http\Controllers\Controller;
use App\Modules\Config\Entity\DomainManager;
use FastDog\User\Entity\UserEmailSubscribe;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class UserSubscribeTableController
 * @package FastDog\User\Http\Controllers\Admin
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserSubscribeTableController extends Controller implements TableControllerInterface
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
     * @param UserEmailSubscribe $model
     */
    public function __construct(UserEmailSubscribe $model)
    {
        parent::__construct();
        $this->model = $model;
        $this->accessKey = $this->model->getAccessKey();
        $this->initTable();
        $this->page_title = trans('app.Подписки');
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
     * Обновление параметров элемента каталога из списка
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postItemSelfUpdate(Request $request)
    {
        $result = ['success' => true, 'items' => []];
        $data = $request->all();
        switch ($data['field']) {
            default:
                $this->updatedModel($data, UserEmailSubscribe::class);
                break;
        }

        return $this->json($result, __METHOD__);
    }

    /**
     * Выгрузка подписок в csv
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getSubscribeCsv()
    {
        $target = public_path() . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'files' .
            DIRECTORY_SEPARATOR . 'subscribe.csv';
        $h = fopen($target, "w+");
        /**
         * @var $items Collection
         */
        $items = UserEmailSubscribe::where(function (Builder $query) {
            $query->where(UserEmailSubscribe::SITE_ID, DomainManager::getSiteId());
        })->get();
        $items->each(function (UserEmailSubscribe $item) use ($h) {
            fputcsv($h, [$item->{UserEmailSubscribe::EMAIL}]);
        });
        fclose($h);

        return response()->download($target)->deleteFileAfterSend(true);
    }
}