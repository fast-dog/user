<?php
namespace FastDog\User\Listeners;



use FastDog\Core\Models\DomainManager;
use FastDog\Core\Models\ModuleManager;
use FastDog\User\Events\UserAdminPrepare as UserAdminPrepareEvent;
use FastDog\User\User;
use Illuminate\Http\Request;

/**
 * Обработка данных в разделе администрирования
 *
 * Событие добавляет дополнительные поля параметров в модель в случае их отсутствия
 *
 * @package App\Modules\Menu\Listeners
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserAdminPrepare
{

    /**
     * @var Request $request
     */
    protected $request;

    /**
     * AfterSave constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param UserAdminPrepareEvent $event
     * @return void
     */
    public function handle(UserAdminPrepareEvent $event)
    {
        /**
         * @var $moduleManager ModuleManager
         */
        $moduleManager = \App::make(ModuleManager::class);
        /**
         * @var $item User
         */
        $item = $event->getItem();
        /**
         * @var $data array
         */
        $data = $event->getData();

        //Состояние
        $data[User::STATUS] = array_first(array_filter(User::getStatusList(), function ($element) use ($data) {
            return ($element['id'] == $data[User::STATUS]);
        }));

        //Состояние
        $data[User::TYPE] = array_first(array_filter(User::getAllType(), function ($element) use ($data) {
            return ($element['id'] == $data[User::TYPE]);
        }));

        //Доступ
        $data[User::SITE_ID] = array_first(array_filter(DomainManager::getAccessDomainList(),
            function ($element) use ($data) {
                return $element['id'] == $data[User::SITE_ID];
            }));

        if (!isset($data['item']['id'])) {
            $data['item']['id'] = 0;
        }

        $event->setData($data);
    }
}