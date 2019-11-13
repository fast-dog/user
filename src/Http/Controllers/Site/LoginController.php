<?php

namespace FastDog\User\Http\Controllers\Site;


use FastDog\Core\Http\Controllers\Controller;
use FastDog\Core\Models\DomainManager;
use FastDog\User\Models\User;
use FastDog\User\Http\Request\ResetPassword;
use Illuminate\Http\Request;
use FastDog\User\Http\Request\Login;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

/**
 * Авторизация
 *
 * @package FastDog\User\Http\Controllers\Site
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string $redirectTo
     */
    protected $redirectTo = '/cabinet';

    /**
     * LoginController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('guest', ['except' => 'logout']);
        $this->middleware('auth', ['except' => 'postLogin']);
    }

    /**
     * Авторизация
     *
     * @param Login $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postLogin(Login $request)
    {
        $request->merge([
            'email' => $request->input('username'),
            User::TYPE => User::USER_TYPE_USER,
            User::STATUS => User::STATUS_ACTIVE,
            User::SITE_ID => DomainManager::getSiteId(),
        ]);

        return $this->login($request);
    }

    /**
     * Страница авторизации
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);


        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        if ($request->ajax()) {
            return response()->json(['success' => false, 'errors' => ['password' => [trans('public.Auth filed...')]]]);
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Поля авторизации
     *
     * @param Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only(User::EMAIL, User::PASSWORD, User::TYPE, User::STATUS, User::SITE_ID);
    }

    /**
     * Сброс пароля
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function confirmPassword(Request $request)
    {
        $hash = base64_decode(\Route::input('hash'));
        $user = User::where([
            User::STATUS => User::STATUS_RESTORE_PASSWORD,
            User::HASH => $hash,
        ])->first();

        if ($user) {
            User::where(['id' => $user->id])->update([
                User::HASH => \DB::raw('null'),
                User::STATUS => User::STATUS_ACTIVE,
            ]);
            \Auth::loginUsingId($user->id, true);

            \Session::flash('message', trans('app.Аккаунт успешно активирован, Ваш пароль изменен, добро пожаловать!!!'));

            return redirect('/cabinet');

        } else {
            \Session::flash('message', trans('app.Пользователь не найден'));

            return redirect('/login');
        }
    }

    /**
     * Подтверждение аккаунта
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function confirm(Request $request)
    {
        $hash = base64_decode(\Route::input('hash'));
        $user = User::where([
            User::STATUS => User::STATUS_NOT_CONFIRMED,
            User::HASH => $hash,
        ])->first();

        if ($user) {
            User::where(['id' => $user->id])->update([
                User::HASH => \DB::raw('null'),
                User::STATUS => User::STATUS_ACTIVE,
            ]);
            \Auth::loginUsingId($user->id, true);

            \Session::flash('message', trans('app.Аккаунт успешно активирован, добро пожаловать!!!'));

            return redirect('/cabinet');

        } else {
            \Session::flash('message', trans('app.Пользователь не найден, возможно активация аккаунта была произведена рание'));

            return redirect('/login');
        }
    }

    /**
     * Отправка нового пароля
     *
     * @param ResetPassword $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function sendPassword(ResetPassword $request)
    {
        /**
         * @var $user User
         */
        $user = User::where([
            User::EMAIL => $request->input('email'),
        ])->first();

        if ($user) {
            $password = $user->quickRandom(8);
            $hash = md5($password . $user->{User::EMAIL} . time());
            User::where('id', $user->id)->update([
                User::PASSWORD => \Hash::make($password),
                User::HASH => $hash,
                User::STATUS => User::STATUS_RESTORE_PASSWORD,
            ]);
            \Session::flash('message', trans('app.Инструкции по восстановлению доступа высляны на указанный адрес.'));

            Emails::send('new_password', [
                'email' => $user->{User::EMAIL},
                'password' => $password,
                'to' => $user->{User::EMAIL},
                'confirm_link' => url('/confirm-password/' . base64_encode($hash), [], config('app.use_ssl')),
            ]);
        }

        return redirect('/login');
    }


}
