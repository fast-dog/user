<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 08.04.2017
 * Time: 8:43
 */

namespace FastDog\User\Controllers\Site;

use App\Core\BaseModel;
use App\Http\Controllers\HomeController;
use App\Modules\Catalog\Entity\CatalogItems;
use App\Modules\Catalog\Entity\CatalogItemsFavoritesStorage;
use App\Modules\Config\Entity\DomainManager;
use App\Modules\Config\Entity\Service;
use App\Modules\DataSource\DataSource;
use App\Modules\Media\Entity\Gallery;
use App\Modules\Media\Entity\GalleryItem;
use FastDog\User\Entity\MessageManager;
use FastDog\User\Entity\Profile\UserProfile;
use FastDog\User\Entity\Profile\UserProfileStat;
use FastDog\User\Entity\UserServices;
use FastDog\User\Entity\UserSettings;
use FastDog\User\Entity\View\UserFavorites;
use FastDog\User\Events\AddChatMessage;
use FastDog\User\Events\PrepareMessage;
use FastDog\User\Request\AddAttach;
use FastDog\User\Request\ChangePassword;
use FastDog\User\Request\ChangePhoto;
use FastDog\User\Request\UpdateProfile;
use FastDog\User\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;
use Nahid\Talk\Conversations\Conversation;
use Nahid\Talk\Messages\Message;

/**
 * Кабинет пользователя
 *
 * @package FastDog\User\Controllers\Site
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class CabinetController extends HomeController
{

    /**
     * RegistrationController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['getPublicProfile']]);
    }

    /**
     * Контент модуля
     *
     * Метод генерирует HTML согласно парамтерам пункта меню
     *
     * @param Request $request
     * @param \App\Modules\Menu\Entity\Menu $item
     * @param $data
     * @return mixed
     * @throws \Throwable
     */
    public function prepareContent(Request $request, $item, $data): \Illuminate\View\View
    {
        if (\Auth::guest()) {
            return redirect(url('login'));
        }
        /**
         * @var $user User
         */
        $user = \Auth::getUser();

        $user->{User::DATA} = json_decode($user->{User::DATA});

        $viewData = [
            'menuItem' => $item,
            'data' => $data,
            'path' => $item->getPath(),
            'metadata' => $item->getMetadata(),
            'theme' => DomainManager::getAssetPath(),
            'user' => $user,
            'inboxes' => null,
            'conversation' => null,
            'conversation_id' => null,
            'request' => $request,
            'items' => [],
            'disabled_sent' => false,
            'item' => [
                'name' => $item->getName(),
            ],
        ];

        if (is_string($viewData['user']->profile->data)) {
            $viewData['user']->profile->data = json_decode($viewData['user']->profile->data);
        }

        if (isset($data['data']->route_data->type)) {
            switch ($data['data']->route_data->type) {
                case User::TYPE_CABINET_MESSAGES:
                    /**
                     * @var $messageManager MessageManager
                     */
                    $messageManager = \App::make(MessageManager::class);
                    $messageManager->auth($user->id);

                    $viewData['messageManager'] = $messageManager;

                    if ($request->has('delete_con')) {
                        $messageManager->deleteConversations($request->input('delete_con'));
                        \Session::flash('message', trans('public.Чат удален.'));

                        return redirect()->back();
                    }
                    $viewData['messages'] = [];
                    Date::setLocale(config('app.locale'));

                    if (!$request->has('con')) {
                        if ($request->input('new') === 'Y') {
                            $messages = $messageManager->getUnreadMessages();
                            if (null !== $messages) {
                                foreach ($messages as $message) {
                                    array_push($viewData['messages'], $this->getMessageData($message));
                                }
                            }
                        } else {
                            $viewData['inboxes'] = $messageManager->getInbox();

                            foreach ($viewData['inboxes'] as $inbox) {
                                if ($inbox->thread) {
                                    $message = $inbox->thread;
                                    $messageData = $this->getMessageData($message);
                                    $messageData['conversation_link'] = \FastDog\User\Entity\User::getUserConversationLink($inbox->withUser->id);
                                    array_push($viewData['messages'], $messageData);
                                }
                            }
                        }
                    } else if ($request->has('con')) {

                        $viewData['conversation'] = $messageManager->getConversationsById($request->input('con'), 0, self::PAGE_SIZE);

                        $viewData['conversation_pages'] = $messageManager->getPages();


                        if ($viewData['conversation']) {
                            $viewData['conversation_id'] = $request->input('con');
                            $viewData['recipient'] = User::find($viewData['conversation']->withUser->id);

                            $viewData['messages'] = [];
                            $lastDay = 0;
                            Date::setLocale('ru');
                            foreach ($viewData['conversation']->messages as $message) {
                                $msgDay = (int)$message->created_at->format('d');
                                $messageData = $this->getMessageData($message);
                                $messageData['dateline'] = ($msgDay != $lastDay);
                                if ($messageData['dateline']) {
                                    $messageData['dateline_text'] = Date::createFromTimestamp($message->created_at->timestamp)->format('d F');
                                }
                                $messageData['answer'] = ($message->sender->id !== $user->id) ? true : false;
                                $lastDay = (int)$message->created_at->format('d');
                                if ($message->is_seen == 0 && $message->user_id <> $user->id) {
                                    $messageManager->makeSeen($message->id);
                                }
                                array_push($viewData['messages'], $messageData);
                            }
                        }
                    }

                    if ($user_id = $request->input('user_id', null)) {
                        /**
                         * @var $recipient User
                         */
                        $recipient = User::find($user_id);

                        //не отправлять самому себе!!!
                        if ($recipient->id == $user->id) {
                            $recipient = null;
                        }
                        /**
                         * Проверка возможности отправки сообщения пользователю
                         */
                        if ($recipient) {
                            $serviceConversationId = $messageManager->isConversationExists($recipient->id);
                            $viewData['recipient'] = $recipient;

                            if ($serviceConversationId) {
                                return view('redirect', ['to' => 'cabinet/messages?con=' . $serviceConversationId]);
                                // return redirect('cabinet/messages?con=' . $serviceConversationId);
                            } else {
                                $serviceConversation = Conversation::create([
                                    'user_one' => $user->id,
                                    'user_two' => $recipient->id,
                                ]);

                                return view('redirect', ['to' => 'cabinet/messages?con=' . $serviceConversation->id]);

//                                return redirect('cabinet/messages?con=' . $serviceConversation->id);
                            }
                        } else {
                            $request->session()->flash('message', trans('public.Невозможно отправить сообщение выбранному пользователю.'));

                            return view('redirect', ['to' => '/cabinet/messages']);
                            // return redirect('/cabinet/messages');
                        }
                    }

                    if (isset($viewData['recipient']) && $viewData['recipient'] !== null) {
                        if ($viewData['recipient']->setting->can('send_personal_messages') === false) {
                            $viewData['disabled_sent'] = true;
                            $request->session()->flash('message', trans('public.Пользователь :name запретил отправлять ему личные сообщения.', [
                                'name' => $viewData['recipient']->getName(),
                            ]));
                        }
                    }

                    $viewData['attach'] = $messageManager->getEmptyAttach();

                    break;
                case User::TYPE_CABINET_NEW_MESSAGES:
                    /**
                     * @var $messageManager MessageManager
                     */
                    $messageManager = \App::make(MessageManager::class);
                    $viewData['messageManager'] = $messageManager;

                    $viewData['recipient'] = User::find($request->input('uid'));
                    $viewData['messages'] = false;
                    if ($viewData['recipient']) {
                        $viewData['messages'] = $messageManager->getMessagesByUserId($viewData['recipient']->id);
                    }
                    break;
                case User::TYPE_CABINET_EDIT:
                    $viewData['user_data'] = $user->getData();
                    break;
                case User::TYPE_CABINET:
                    $viewData['items'] = [];
                    break;
                case User::TYPE_CABINET_FAVORITES:
                    $viewData['type'] = $request->input('type', 'items');
                    switch ($viewData['type']) {
                        case 'items':
                            $viewData['items'] = CatalogItems::getFavorites($user->id, 9);
                            break;
                        case 'users':
                            $viewData['items'] = UserFavorites::getFavorites($user->id, 9);
                            break;
                        default:
                            $viewData['items'] = CatalogItems::getFavorites($user->id, 9);
                            break;
                    }
                    break;
                case User::TYPE_CABINET_SETTINGS:
                    $viewData['setting'] = $user->setting;
                    break;
                case User::TYPE_CABINET_BILLING:
                    /**
                     * @var $service  Service
                     */
                    $service = Service::where('alias', 'count_items')->first();
                    if ($service) {
                        $checkUserService = UserServices::where([
                            UserServices::SERVICE_ID => $service->id,
                            UserServices::USER_ID => $user->id,
                        ])->first();

                        $viewData['service'] = $service;
                        $viewData['checkUserService'] = $checkUserService;
                    }
                    break;
            }
        }


        if (isset($data['data']->template) && $data['data']->template <> '') {
            if (view()->exists($data['data']->template)) {
                $viewData['trans_key'] = str_replace(['.', '::'], '/', $data['data']->template);
                view()->share($viewData);
                $viewData['menuItem']->success();

                return view($data['data']->template, $viewData);
            }
        }

        view()->share($viewData);

        return parent::prepareContent($request, $item, $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDeleteMessagesAttach(Request $request)
    {
        $result = [
            'success' => true,
        ];
        /**
         * @var $user User
         */
        $user = \Auth::getUser();

        $check = GalleryItem::where([
            GalleryItem::USER_ID => $user->id,
            GalleryItem::PARENT_TYPE => GalleryItem::TYPE_CHAT_MESSAGE,
            'id' => $request->input('id'),
        ])->first();

        if ($check) {
            GalleryItem::deleteFile($check->id);
        }

        return $this->json($result, __METHOD__);
    }

    /**
     * @param AddAttach $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postMessagesAttach(AddAttach $request)
    {
        $result = [
            'success' => false,
        ];
        /**
         * @var $user User
         */
        $user = \Auth::getUser();

        if ($user->isBanned()) {
            return response()->json(['success' => false]);
        }
        if ($request->file('attach')) {
            $path = $request->attach->store('', 'attach');
            $thumb = Gallery::getPhotoThumb('/upload/attach/' . $path, 120);
            $item = GalleryItem::create([
                GalleryItem::PARENT_ID => (int)$request->input('parent_id', 0),
                GalleryItem::USER_ID => $user->id,
                GalleryItem::PARENT_TYPE => GalleryItem::TYPE_CHAT_MESSAGE,
                GalleryItem::PATH => '/upload/attach/' . $path,
                GalleryItem::HASH => md5('/upload/attach/' . $path),
                GalleryItem::DATA => json_encode([
                    'file' => '/upload/attach/' . $path,
                    'thumb' => $thumb,
                ]),
            ]);
            $result['item'] = [
                'id' => $item->id,
                'name' => $item->getName(),
                'path' => $item->{GalleryItem::PATH},
                'thumb' => $thumb,
            ];
            $result['success'] = true;
        }

        return $this->json($result, __METHOD__);
    }

    /**
     * Набор данных по сообщению чата
     *
     * @param $message
     * @return array
     */
    private function getMessageData($message)
    {
        $data = [
            'id' => $message->id,
            'message' => $message->message,
            'photo' => $message->user->getPhoto(128),
            'user_name' => $message->user->getName(),
            'is_online' => $message->user->isOnline(),
            'humans_time' => Date::createFromTimestamp($message->created_at->timestamp)->diffForHumans(),
            'format_time' => $message->created_at->format('d.m.Y H:i'),
            'sender_id' => $message->user->id,
            'sender_public_link' => $message->sender->getPublicLink(),
            'conversation_link' => \FastDog\User\Entity\User::getUserConversationLink($message->user->id),
            'sender_in_favorites' => \FastDog\User\Entity\User::checkUserInFavorites($message->user->id),
        ];
        \Event::fire(new PrepareMessage($message, $data));

        return $data;
    }

    /**
     * Сохранение профиля
     *
     * @param UpdateProfile|Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveProfile(UpdateProfile $request)
    {
        /**
         * @var $user User
         */
        $user = \Auth::getUser();

        if ($request->file('photo')) {
            $user = $this->setPhoto($user, $request);
        }
        if ($request->input('delete_photo')) {
            $this->deletePhoto($user);
        }
        $profile = $user->profile;
        $profileData = clean($request->input('profile'));

        $updateData = [];

        if ($user->email !== $request->input(User::EMAIL)) {
            $updateData[User::EMAIL] = $request->input(User::EMAIL);
            $sendData = [
                'user' => $user,
                'user_name' => $user->getName(),
                'date' => Carbon::now()->format('Y.m.d H:i'),
                'to' => $updateData[User::EMAIL],
                'email' => $updateData[User::EMAIL],
            ];
            $this->dispatch(new SendNotify('system:change_email', $sendData, $sendData['user']));
        }

        if ($request->input(User::LOGIN)) {
            $updateData[User::LOGIN] = clean($request->input(User::LOGIN));
        }

        if (count($updateData)) {
            User::where('id', $user->id)->update($updateData);
        }

        UserProfile::where('id', $profile->id)->update([
            UserProfile::NAME => clean($request->input('profile.' . UserProfile::NAME)),
            UserProfile::SURNAME => clean($request->input('profile.' . UserProfile::SURNAME)),
            UserProfile::PHONE => clean($request->input('profile.' . UserProfile::PHONE)),
            UserProfile::DATA => json_encode($profileData),
        ]);

        \Session::flash('message', trans('public.Ваш профиль обновлен.'));

        return redirect('/cabinet');
    }

    /**
     * Обновление пароля
     *
     * @param ChangePassword $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSavePassword(ChangePassword $request)
    {
        /**
         * @var $user User
         */
        $user = \Auth::getUser();

        User::where('id', $user->id)->update([
            User::PASSWORD => \Hash::make($request->input('new_' . User::PASSWORD)),
        ]);

        \Session::flash('message', trans('public.Текущий пароль обновлен.'));

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
            ]);
        }

        return redirect('/cabinet');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postNewMessage(Request $request)
    {
        $message = null;
        if ($request->input('message', null) !== '') {
            /**
             * @var $messageManager MessageManager
             */
            $messageManager = \App::make(MessageManager::class);
            if ($request->input('conversation_id')) {
                $message = $messageManager->sendMessage($request->input('conversation_id'), $request->input('message'));
            } else {
                $message = $messageManager->sendMessageByUserId($request->input('recipient'), $request->input('message'));
            }
            \Event::fire(new AddChatMessage($message, $request));
        }

        if ($request->ajax() && $message) {
            $messageData = $this->getMessageData($message);

            return response()->json([
                'success' => true,
                'message' => $messageData,
            ]);
        }

        return redirect()->back();
    }

    /**
     * Изменение фото-аватара
     *
     * @param ChangePhoto $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postChangePhoto(ChangePhoto $request)
    {
        /**
         * @var $user User
         */
        $user = \Auth::getUser();

        if ($request->file('photo')) {
            $user = $this->setPhoto($user, $request);

            return response()->json([
                'success' => true,
                'file' => $user->getPhoto(128),
            ]);
        }

        return response()->json([
            'success' => false,
        ]);
    }

    /**
     * Удаление фото\автара
     *
     * @param User $user
     */
    private function deletePhoto(User $user)
    {
        $userData = $user->data;
        if (is_string($userData)) {
            $userData = json_decode($userData, true);
        }
        if (isset($userData['photo_id'])) {
            GalleryItem::deleteFile($userData['photo_id']);
        }
        $userData['photo_id'] = null;
        User::where('id', $user->id)->update([
            'data' => json_encode($userData),
        ]);
    }

    /**
     * Обновляет фотографию профиля
     *
     * @param User $user
     * @param Request $request
     * @return User
     */
    private function setPhoto(User $user, Request $request)
    {
        $userData = $user->data;
        if (is_string($userData)) {
            $userData = json_decode($userData, true);
        }

        if (isset($userData['photo_id'])) {
            GalleryItem::deleteFile($userData['photo_id']);
        }
        $path = $request->photo->store('users', 'images');
        $thumb = Gallery::getPhotoThumb('/upload/images/' . $path, 250);

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
        $userData['photo_id'] = $item->id;
        User::where('id', $user->id)->update([
            'data' => json_encode($userData),
        ]);
        $user = User::find($user->id);
        if (config('cache.default') == 'redis') {
            \Cache::tags(['user#' . $user->id])->flush();
        }

        return $user;
    }

    /**
     * Проверка местоположения пользователей
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCheckLocality(Request $request)
    {
        $model = new BaseModel();
        $model = $model->setTable('data_source_cities');

        $item = $model->where([
            'alias' => $request->input('locality') . '-' . $request->input('country'),
        ])->first();
        if (null === $item) {
            $model->fill([
                'name' => title_case($request->input('locality')),
                'alias' => $request->input('locality') . '-' . $request->input('country'),
                'state' => 1,
                'data' => json_encode([
                    'auto_create' => 'Y',
                    'country' => title_case($request->input('country')),
                ]),
            ]);
            $item = $model->save();
            if ($item) {
                $item = $model;
            }
            if (config('cache.default') == 'redis') {
                \Cache::tags([DataSource::CACHE_KEY_DATA_SOURCE_CITY])->flush();
            } else {
                \Cache::forget(DataSource::CACHE_KEY_DATA_SOURCE_CITY);
            }

            return response()->json([
                'create' => true,
                'select_id' => $item->id,
                'id' => $item->id,
                'name' => $item->name,
                'success' => true,
            ]);
        }
        if ($item) {
            return response()->json([
                'create' => false,
                'id' => $item->id,
                'name' => $item->name,
                'select_id' => $item->id,
                'success' => true,
            ]);
        }

        return response()->json([
            'success' => false,
        ]);
    }

    /**
     * Удаление объявления из закладок
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteFavorites(Request $request)
    {
        /**
         * @var $user User
         */
        $user = \Auth::getUser();

        $deleteItem = CatalogItemsFavoritesStorage::where([
            CatalogItemsFavoritesStorage::USER_ID => $user->id,
            CatalogItemsFavoritesStorage::CONST_ID => $request->input(CatalogItemsFavoritesStorage::CONST_ID),
        ])->delete();
        if ($deleteItem) {
            return response()->json([
                'success' => true,
            ]);
        }

        return response()->json([
            'success' => false,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUserFavorites(Request $request)
    {
        /**
         * @var $user User
         */
        $user = \Auth::getUser();

        $deleteItem = \FastDog\User\Entity\UserFavorites::where([
            \FastDog\User\Entity\UserFavorites::USER_ID => $user->id,
            \FastDog\User\Entity\UserFavorites::ITEM_ID => $request->input('id', 0),
        ])->delete();
        if ($deleteItem) {
            return response()->json([
                'success' => true,
            ]);
        }

        return response()->json([
            'success' => false,
        ]);
    }

    /**
     * Обновление настроек пользователя
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSetting(Request $request)
    {
        /**
         * @var $user User
         */
        $user = \Auth::getUser();

        $data = [
            UserSettings::SHOW_PROFILE => $request->input('setting.' . UserSettings::SHOW_PROFILE, null) == null ? 0 : 1,
            UserSettings::SEND_PERSONAL_MESSAGES => $request->input('setting.' . UserSettings::SEND_PERSONAL_MESSAGES, null) == null ? 0 : 1,
            UserSettings::SEND_EMAIL_NOTIFY => $request->input('setting.' . UserSettings::SEND_EMAIL_NOTIFY, null) == null ? 0 : 1,
        ];

        UserSettings::where([
            UserSettings::USER_ID => $user->id,
        ])->update($data);
        if ($data[UserSettings::SHOW_PROFILE] == 1) {
            \Session::flash('message', trans('public.Ваши настройки обновлены. Профиль скрыт от публичного доступа.'));
        } else {
            if ($data[UserSettings::SHOW_PROFILE] == 0) {

            }
            \Session::flash('message', trans('public.Текущие настройки обновлены.'));
        }


        return redirect('/cabinet');
    }

    /**
     * Пуличный просмотр профиля
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function getPublicProfile(Request $request)
    {
        $condition = [];
        $viewData = [
            'metadata' => [],
            'showEdit' => false,
        ];
        if ($id = \Route::input('id', null)) {
            $condition['id'] = $id;
        }
        if ($login = \Route::input('login', null)) {
            $condition['login'] = $login;
        }

        /**
         * @var $user User
         */
        $user = User::where($condition)->first();
        if ($user) {
            $viewData['user'] = $user;
            $viewData['items'] = CatalogItems::getItemsInProfile($request, 9, $user->id);
            $viewData['metadata'] = $user->getProfileMetatag();

            if ($user->setting->can(UserSettings::SHOW_PROFILE) === true) {
                return abort(404); #view('public.001.modules.users.cabinet.hidden-profile');
            }
            if (\Auth::guest()) {
                UserProfileStat::add($user->id);
            } else {
                $currentUser = \Auth::getUser();
                UserProfileStat::add($user->id, $currentUser->id);
            }

            return view('public.001.modules.users.public-profile', $viewData);
        }

        return abort(404);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postAddFavorites(Request $request)
    {
        /**
         * @var $user
         */
        $user = \Auth::getUser();
        $result = ['success' => true, 'status' => false];

        $favItem = \FastDog\User\Entity\UserFavorites::where([
            \FastDog\User\Entity\UserFavorites::USER_ID => $user->id,
            \FastDog\User\Entity\UserFavorites::ITEM_ID => $request->input('id'),
        ])->first();
        if (!$favItem) {
            \FastDog\User\Entity\UserFavorites::create([
                \FastDog\User\Entity\UserFavorites::USER_ID => $user->id,
                \FastDog\User\Entity\UserFavorites::ITEM_ID => $request->input('id'),
            ]);

            $result['status'] = 'create';
        } else {
            $favItem->delete();
            $result['status'] = 'deleted';
        }

        return response()->json($result);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postMessages(Request $request)
    {
        $result = ['success' => true, 'items' => []];
        $user = \Auth::getUser();
        /**
         * @var $messageManager MessageManager
         */
        $messageManager = \App::make(MessageManager::class);

        $items = $messageManager->getConversationsById($request->input('id'), 0, self::PAGE_SIZE, $request->input('page'));
        /**
         * @var $item Message
         */
        foreach ($items->messages as $item) {

            array_unshift($result['items'], $this->getMessageData($item));

            if ($item->is_seen == 0 && $item->user_id <> $user->id) {
                $messageManager->makeSeen($item->id);
            }
        }

        return response()->json($result);
    }

    /**
     * Удаление сообщений в чате
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postClearMessages(Request $request)
    {
        /**
         * @var $user
         */
        $user = \Auth::getUser();
        /**
         * @var $messageManager MessageManager
         */
        $messageManager = \App::make(MessageManager::class);
        $messageManager->auth($user->id);
        $messageManager->clearConversation($request->input('con_id'));

        return response()->json(['success' => true]);
    }

}