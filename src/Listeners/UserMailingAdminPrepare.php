<?php

namespace FastDog\User\Listeners;

use FastDog\Core\Models\ModuleManager;
use FastDog\Media\Models\GalleryItem;
use FastDog\User\Events\UserMailingAdminPrepare as EventUserMailingAdminPrepare;

use FastDog\User\Models\UserMailing;
use FastDog\User\Models\UserMailingTemplates;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * При редактирование в разделе администрирования
 *
 * @package App\Modules\Content\Listeners
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserMailingAdminPrepare
{
    /**
     * @var Request $request
     */
    protected $request;

    /**
     * ContentAdminPrepare constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param EventUserMailingAdminPrepare $event
     * @return void
     */
    public function handle(EventUserMailingAdminPrepare $event)
    {
        /**
         * @var $moduleManager ModuleManager
         */
        $moduleManager = \App::make(ModuleManager::class);

        $data = $event->getData();
        $item = $event->getItem();

        //Состояние
        $data[UserMailing::TEMPLATE_ID] = Arr::first(array_filter(UserMailingTemplates::getList(), function ($element) use ($data) {
            return ($element['id'] == $data[UserMailing::TEMPLATE_ID]);
        }));


        $data['files_module'] = ($moduleManager->hasModule('App\Modules\Media\Media')) ? 'Y' : 'N';

        $data['el_finder'] = [
            GalleryItem::PARENT_TYPE => GalleryItem::TYPE_MAILING,
            GalleryItem::PARENT_ID => (isset($item->id)) ? $item->id : 0,
        ];

        if (config('app.debug')) {
            $data['_events'][] = __METHOD__;
        }

        $event->setData($data);
    }
}
