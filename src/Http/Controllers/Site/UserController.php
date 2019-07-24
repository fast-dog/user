<?php

namespace FastDog\User\Http\Controllers\Site;


use FastDog\Config\Models\Emails;
use FastDog\Core\Http\Controllers\Controller;
use FastDog\Core\Models\DomainManager;
use FastDog\User\Models\UserEmailSubscribe;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;

/**
 * Публичная часть
 *
 * @package FastDog\User\Http\Controllers\Site
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserController extends Controller
{
    use  ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string $redirectTo
     */
    protected $redirectTo = '/cabinet';

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
                \Session::flash('message_subscribe', trans('public.Ваша подписка отменена.'));
                Emails::send($email->{Emails::ALIAS}, [
                    'to' => $subscribe->{UserEmailSubscribe::EMAIL},
                    Emails::SITE_ID => DomainManager::getSiteId(),
                ]);
            }
        }

        return redirect('/');
    }
}