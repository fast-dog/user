<?php

namespace FastDog\User\Listeners;


use App\Modules\Config\Entity\DomainManager;
use App\Modules\Media\Entity\Gallery;
use App\Modules\Media\Entity\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Nette\Mail\Message;

/**
 * Информация о сообщение чата
 *
 * Событие вызывается при обрбаотке сообщений чата
 *
 * @package FastDog\User\Listeners
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class PrepareMessage
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * UpdateProfile constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param \FastDog\User\Events\PrepareMessage $event
     */
    public function handle(\FastDog\User\Events\PrepareMessage $event)
    {
        /**
         * @var $message Message
         */
        $message = $event->getMessage();

        /**
         * @var $items Collection
         */
        $items = GalleryItem::where([
            GalleryItem::PARENT_ID => $message->id,
            GalleryItem::PARENT_TYPE => GalleryItem::TYPE_CHAT_MESSAGE,
        ])->get();
        $data = $event->getData();

        if ($items->isNotEmpty()) {
            $template = 'public.' . DomainManager::getSiteId() . '.modules.users.cabinet.messages.partials.image-attachment';
            if (view()->exists($template)) {
                $data['message'] .= '<ul class="list-inline img-uploaded">';
                $items->each(function (GalleryItem $item, $idx) use (&$data) {
                    $thumb = Gallery::getPhotoThumb(str_replace([url('/')], '', $item->{GalleryItem::PATH}), 120);
                    $data['message'] .= view('public.' . DomainManager::getSiteId() . '.modules.users.cabinet.messages.partials.image-attachment', [
                        'image' => [
                            'id' => $item->id,
                            'path' => $item->{GalleryItem::PATH},
                            'thumbs' => $thumb,
                            'delete' => false,
                        ],
                    ])->render();
                });
                $data['message'] .= '</ul>';
            } else {
                $data['message'] .= '<br /><strong style="color: red;">Attachment error, template "' . $template . '" not found</strong>';
            }
        }

        if (config('app.debug')) {
            $data['message'] .= '<br />##-' . $data['id'] . '-##';
        }

        $event->setData($data);
    }
}
