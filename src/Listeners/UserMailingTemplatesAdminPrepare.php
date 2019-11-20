<?php

namespace FastDog\User\Listeners;



use FastDog\Core\Models\ModuleManager;
use FastDog\Media\Models\GalleryItem;
use FastDog\User\Events\UserMailingTemplatesAdminPrepare as EventUserMailingTemplatesAdminPrepare;

use Illuminate\Http\Request;

/**
 * При редактирование в разделе администрирования
 *
 * @package App\Modules\Content\Listeners
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserMailingTemplatesAdminPrepare
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
     * @param EventUserMailingTemplatesAdminPrepare $event
     * @return void
     */
    public function handle(EventUserMailingTemplatesAdminPrepare $event)
    {
        /**
         * @var $moduleManager ModuleManager
         */
        $moduleManager = \App::make(ModuleManager::class);

        $data = $event->getData();
        $item = $event->getItem();

        $data['files_module'] = ($moduleManager->hasModule('App\Modules\Media\Media')) ? 'Y' : 'N';

        $data['el_finder'] = [
            GalleryItem::PARENT_TYPE => GalleryItem::TYPE_MAILING,
            GalleryItem::PARENT_ID => (isset($item->id)) ? $item->id : 0,
        ];

        $data['properties'] = $item->properties();

        if (config('app.debug')) {
            $data['_events'][] = __METHOD__;
        }

        $event->setData($data);
    }
}
