<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 18.01.2017
 * Time: 19:11
 */

namespace FastDog\User\Http\Controllers\Site;


use App\Http\Controllers\HomeController;
use App\Modules\Config\Entity\DomainManager;
use App\Modules\Config\Entity\Emails;
use FastDog\User\Entity\UserEmailSubscribe;
use FastDog\User\User;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

/**
 * Публичная часть
 *
 * @package FastDog\User\Http\Controllers\Site
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserController extends HomeController
{
    use  ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string $redirectTo
     */
    protected $redirectTo = '/cabinet';

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
        return parent::prepareContent($request, $item, $data);
    }

    /**
     * Создание подписки на расылку новостей и рочего контента
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function postSubscribe(Request $request)
    {
        $result = [
            'success' => false,
            'message' => trans('public.Вы подписаны на рассылку'),
        ];
        /**
         * @var $validator \Illuminate\Validation\Validator
         */
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'error' => trans('public.Ошибка подписки на рассылку'),
                ]);
            }

            return redirect()->back(302)->withErrors($validator)->withInput();
        } else {
            $hash = md5($request->input(UserEmailSubscribe::EMAIL) . '^_^' . DomainManager::getSiteId());
            $data = [
                UserEmailSubscribe::EMAIL => $request->input(UserEmailSubscribe::EMAIL),
                UserEmailSubscribe::SITE_ID => DomainManager::getSiteId(),
                UserEmailSubscribe::HASH => $hash,
            ];

            /**
             * @var $subscribe UserEmailSubscribe
             */
            $subscribe = UserEmailSubscribe::firstOrCreate($data);

            $email = Emails::where([
                Emails::ALIAS => 'subscribe_success',
                Emails::SITE_ID => DomainManager::getSiteId(),
            ])->first();

            if ($email) {
                Emails::send($email->{Emails::ALIAS}, [
                    'to' => $request->input(UserEmailSubscribe::EMAIL),
                    'LINK' => url('subscribe/off?hash=' . $hash),
                    Emails::SITE_ID => DomainManager::getSiteId(),
                ]);
            }
            $result['success'] = true;
            if ($request->ajax()) {
                 return $this->json($result, __METHOD__);
            }
        }
        return redirect()->back(302);
    }

    /**
     * Удаление подписки по идентификатору
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function getSubscribeOff(Request $request)
    {
        $hash = $request->input('hash');
        /**
         * @var $subscribe UserEmailSubscribe
         */
        $subscribe = UserEmailSubscribe::where([
            UserEmailSubscribe::SITE_ID => DomainManager::getSiteId(),
            UserEmailSubscribe::HASH => $hash,
        ])->first();

        if ($subscribe) {
            $subscribe->delete();
            $email = Emails::where([
                Emails::ALIAS => 'subscribe_delete',
                Emails::SITE_ID => DomainManager::getSiteId(),
            ])->first();
            if ($email) {
                \Session::flash('message_subscribe',trans('public.Ваша подписка отменена.'));
                Emails::send($email->{Emails::ALIAS}, [
                    'to' => $subscribe->{UserEmailSubscribe::EMAIL},
                    Emails::SITE_ID => DomainManager::getSiteId(),
                ]);
            }


        }
        return redirect('/');
    }
}