<?php

namespace FastDog\User\Listeners;

use App\Modules\Config\Entity\Emails;
use App\Modules\Media\Entity\Gallery;
use App\Modules\Media\Entity\GalleryItem;
use FastDog\User\Entity\UserConfig;
use FastDog\User\Events\UserRegistration as UserRegistrationEvent;
use FastDog\User\User;
use Illuminate\Http\Request;

/**
 * Регистрация
 *
 * Событие вызывается после регистрации пользователя, отправляет уведомление о успешной регистрации или код подтверждения
 *
 * @package FastDog\User\Listeners
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserRegistration
{
    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Обработчик
     *
     * @param UserRegistrationEvent $event
     * @return void
     */
    public function handle(UserRegistrationEvent $event)
    {
        $user = $event->getUser();
        /**
         * @var $config UserConfig
         */
        $config = $user->getPublicConfig();

        switch ($user->type) {
            case User::USER_TYPE_USER:
            case User::USER_TYPE_ADMIN:

                break;
            case User::USER_TYPE_CORPORATE:

                break;
        }
        if ($config !== null && $config->can('registration_confirm')) {
            Emails::send('user_registration_confirm', [
                'title_header' => 'Регистрационная информация',
                'email' => $user->{User::EMAIL},
                'password' => $this->request->input(User::PASSWORD),
                'to' => $user->{User::EMAIL},
                'confirm_link' => url('/confirm/' . base64_encode($user->{User::HASH}), [], config('app.use_ssl')),
            ]);
        } else {
            Emails::send('user_registration', [
                'title_header' => 'Регистрационная информация',
                'email' => $user->{User::EMAIL},
                'password' => $this->request->input(User::PASSWORD),
                'to' => $user->{User::EMAIL},
            ]);
        }

        /**
         * Загруженный через социальные сети аватар
         */
        if ($this->request->has('create_avatar')) {
            $path = $this->request->input('create_avatar');
            $thumb = Gallery::getPhotoThumb('/upload/images/' . $path, 250);
            /**
             * @var $item GalleryItem
             */
            $item = GalleryItem::create([
                GalleryItem::PARENT_ID => $user->id,
                GalleryItem::PARENT_TYPE => GalleryItem::TYPE_USER_PHOTO,
                GalleryItem::PATH => '/upload/images/' . $path,
                GalleryItem::HASH => md5('/upload/images/' . $path),
                GalleryItem::DATA => json_encode([
                    'file' => '/upload/images/' . $path,
                    'thumb' => $thumb,
                ]),
            ]);

            $data = (is_string($user->data)) ? json_decode($user->data) : $user->data;
            $data->photo_id = $item->id;
            User::where('id', $user->id)->update([
                User::DATA => \GuzzleHttp\json_encode($data),
            ]);
        }
    }
}
