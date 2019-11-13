<?php
namespace FastDog\User\Http\Controllers\Site;


use App\Http\Controllers\Controller;
use FastDog\Core\Models\DomainManager;
use FastDog\User\Events\UserRegistration;
use FastDog\User\Events\UserUpdate;
use FastDog\User\Models\UserConfig;
use FastDog\User\Http\Request\Registration;
use FastDog\User\User;

/**
 * Регистрация
 *
 * @package FastDog\User\Http\Controllers\Site
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class RegistrationController extends Controller
{

    /**
     * RegistrationController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Регистрация
     *
     * @param Registration $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postRegistration(Registration $request)
    {
        $user = new User();

        /**
         * @var $config UserConfig
         */
        $config = $user->getPublicConfig();
        $roleUser = DomainManager::getRoleUser(DomainManager::getSiteId());

        $data = [
            User::EMAIL => $request->input(User::EMAIL),
            User::TYPE => $request->input(User::TYPE, User::USER_TYPE_USER),
            User::STATUS => $request->input(User::STATUS, User::STATUS_ACTIVE),
            User::DATA => json_encode([]),
        ];
        if ($request->has(User::PASSWORD) && ($request->input(User::PASSWORD) !== '')) {
            $data[User::PASSWORD] = \Hash::make($request->input(User::PASSWORD));
        }
        if ($config !== null && $config->can('registration_confirm')) {
            $data[User::STATUS] = User::STATUS_NOT_CONFIRMED;
            $data[User::HASH] = md5($data[User::PASSWORD] . $data[User::EMAIL] . time());
        }

        if ($config && $config->can('allow_registration')) {
            $data[User::SITE_ID] = DomainManager::getSiteId();

            $user = User::create($data);

            event(new UserRegistration($user));


            event(new UserUpdate($user, $request));

            if ($config !== null && false == $config->can('registration_confirm')) {
                \Auth::loginUsingId($user->id, true);
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'redirect' => '/cabinet'
                    ]);
                }
                return redirect('/cabinet');
            } else {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => trans('public.На указанную Вами почту выслана инструкция по активации аккаунта')
                    ]);
                }
                \Session::flash('message', trans('public.На указанную Вами почту выслана инструкция по активации аккаунта'));

                return redirect('/login');
            }
        }

        return abort(403);
    }

}