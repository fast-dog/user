<?php

namespace FastDog\User\Listeners;

use FastDog\Media\Models\GalleryItem;
use FastDog\User\User;
use Illuminate\Http\Request;
use FastDog\Media\Events\AfterDeleteFile as EventAfterDeleteFile;

/**
 * После удаления файла
 *
 * Событие вызывается при удаление файла в файловом менеджере, если файл был ассоциирован с учетной записью,
 * в модель будут внесены соответствующие изменения
 *
 * @package FastDog\User\Listeners
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class AfterDeleteFile
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * AfterDeleteFile constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Обработчик события
     *
     * @param EventAfterDeleteFile $event
     * @return void
     */
    public function handle(EventAfterDeleteFile $event)
    {
        $item = $event->getData();
        $result = $event->getResult();
        switch ($item->{GalleryItem::PARENT_TYPE}) {
            case GalleryItem::TYPE_USER_PHOTO:
                $user = User::find($item->{GalleryItem::PARENT_ID});
                if ($user) {
                    $data = json_decode($user->data, true);
                    unset($data['photo_id']);
                    User::where('id', $user->id)->update([
                        'data' => json_encode($data)
                    ]);
                    $user = User::find($item->{GalleryItem::PARENT_ID});
                    $result['photo'] = $user->getPhoto();
                    $event->setResult($result);
                }
                break;
        }
    }
}
