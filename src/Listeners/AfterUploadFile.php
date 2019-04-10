<?php

namespace FastDog\User\Listeners;

use App\Modules\Media\Entity\Gallery;
use App\Modules\Media\Entity\GalleryItem;
use App\Modules\Media\Events\AfterUploadFile as EventAfterUploadFile;

use FastDog\User\User;
use Illuminate\Http\Request;

/**
 * После загрузки файла
 *
 * Событие вызывается после загрузки файла в профиле пользователя, обновляет модель и сохраняет файл
 * в галерею с ассоциативной привязкой к аккаунту пользователя
 *
 * @package App\Modules\Content\Listeners
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class AfterUploadFile
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
     *
     * @param EventAfterUploadFile $event
     * @return void
     */
    public function handle(EventAfterUploadFile $event)
    {
        $result = $event->getResult();
        $data = $event->getData();
        $_data = $this->request->all();
        if (isset($_data[GalleryItem::PARENT_TYPE])) {
            switch ($_data[GalleryItem::PARENT_TYPE]) {
                /**
                 * Загрузка фото профиля
                 */
                case GalleryItem::TYPE_USER_PHOTO:
                    $fileName = $data['filename'];

                    //Параметры устарели, смотреть в FastDog\User\Controllers\Site\CabinetController::saveProfile
                    $thumb = Gallery::getPhotoThumb('/upload/.users/' . $fileName, 100);

                    /**
                     * Обновляем параметры пользователя
                     */
                    $userData = $data['_user']->data;
                    if (is_string($userData)) {
                        $userData = json_decode($userData, true);
                    }

                    /**
                     * Удаляем старое изображение
                     */
                    if (isset($userData['photo_id'])) {
                        GalleryItem::deleteFile($userData['photo_id']);
                    }

                    /**
                     * Сохраняем файл в галерею
                     * @var GalleryItem
                     */
                    $item = GalleryItem::create([
                        GalleryItem::PARENT_ID => $data['_user']->id,
                        GalleryItem::PARENT_TYPE => GalleryItem::TYPE_USER_PHOTO,
                        GalleryItem::PATH => '/upload/.users/' . $fileName,
                        GalleryItem::HASH => md5('/upload/.users/' . $fileName),
                        GalleryItem::DATA => json_encode([
                            'file' => $data['target_dir'] . DIRECTORY_SEPARATOR . $data['filename'],
                            'thumb' => $thumb
                        ])
                    ]);

                    $userData['photo_id'] = $item->id;
                    User::where('id', $data['_user']->id)->update([
                        'data' => json_encode($userData)
                    ]);
                    $result['id'] = $item->id;
                    $result['photo'] = url($thumb['file']);
                    $result['msg'] = 'Файл загружен, учетная запись пользователя обновлена';
                    $result['success'] = true;
                    $event->setResult($result);
                    break;
            }
        }


        $event->setData($data);
    }
}
