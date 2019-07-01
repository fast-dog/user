<?php

namespace FastDog\User\Listeners;


use FastDog\Media\Events\BeforeUploadFile as EventBeforeUploadFile;

use FastDog\Media\Models\GalleryItem;
use FastDog\User\User;
use Illuminate\Http\Request;

/**
 * Перед загрузкой файла
 *
 * Событие вызывается перед загрузкой файла, проверяется доступная для размещения директория, если её нет, будет создана
 *
 * @package App\Modules\Content\Listeners
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class BeforeUploadFile
{
    /**
     * @var Request
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
     * Обработчик
     * @param EventBeforeUploadFile $event
     * @return void
     */
    public function handle(EventBeforeUploadFile $event)
    {
        $data = $event->getData();
        $data['filename'] = $data['file']->getClientOriginalName();
        $_data = $this->request->all();
        switch ($_data[GalleryItem::PARENT_TYPE]) {
            /**
             * Загрузка фото профиля
             */
            case GalleryItem::TYPE_USER_PHOTO:
                $user = User::where('id', $_data[GalleryItem::PARENT_ID])->first();
                if ($user) {
                    $data['success'] = true;
                    $data['_user'] = $user;
                    $data['target_dir'] .= DIRECTORY_SEPARATOR . '.users';
                    if (!is_dir($data['target_dir'])) {
                        mkdir($data['target_dir'], 0755);
                        chmod($data['target_dir'], 0755);
                    }
                }
                break;
        }
        $event->setData($data);
    }
}
