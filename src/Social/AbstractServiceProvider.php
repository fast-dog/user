<?php
namespace FastDog\User\Social;


use FastDog\Core\Models\DomainManager;
use FastDog\User\Events\UserRegistration;
use FastDog\User\User;
use Laravel\Socialite\Facades\Socialite;

/**
 * Абстрактное определение методов для работы с соц. сетями
 *
 * @package FastDog\User\Social
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
abstract class AbstractServiceProvider
{
    protected $provider;

    /**
     *  Create a new SocialServiceProvider instance
     */
    public function __construct()
    {
        $provider = str_replace(
            'serviceprovider', '', strtolower((new \ReflectionClass($this))->getShortName())
        );
        switch ($provider) {
            default:
                $this->provider = Socialite::driver($provider);
                break;
        }

    }

    /**
     *  Logged in the user
     *
     * @param  \FastDog\User\User $user
     * @return \Illuminate\Http\Response
     */
    protected function login($user)
    {
        \Auth::loginUsingId($user->id, true);

        return redirect('/cabinet/');
    }

    /**
     * Дополнительные действия при регистрации пользователя
     *
     * @param array $data
     * @return User $user
     * @internal param array $user
     */
    protected function register(array $data)
    {
        $roleUser = DomainManager::getRoleUser(DomainManager::getSiteId());

        if ($roleUser) {
            $data[User::GROUP_ID] = $roleUser->id;
        }
        if (!is_string($data[User::DATA])) {
            $data[User::DATA] = json_encode($data[User::DATA]);
        }
        $data[User::HASH] = md5($data[User::PASSWORD] . $data[User::EMAIL] . time());
        $user = User::create($data);

        $user->assignRole($roleUser);

        $data = \Request::all();

        \Event::fire(new UserRegistration($user));

        UserProfile::create([
            UserProfile::USER_ID => $user->id,
            UserProfile::NAME => $data[UserProfile::NAME],
            UserProfile::SURNAME => $data[UserProfile::SURNAME],
        ]);

        return $user;
    }

    /**
     *  Redirect the user to provider authentication page
     *
     * @return \Illuminate\Http\Response
     */
    public function redirect()
    {
        return $this->provider->redirect();
    }

    /**
     *  Handle data returned by the provider
     *
     * @return \Illuminate\Http\Response
     */
    abstract public function handle();
}