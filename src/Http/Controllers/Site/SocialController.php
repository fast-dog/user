<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 06.04.2017
 * Time: 11:42
 */

namespace FastDog\User\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use FastDog\User\Social\FacebookServiceProvider;
use FastDog\User\Social\GoogleServiceProvider;
use FastDog\User\Social\TwitterServiceProvider;
use FastDog\User\Social\VkontakteServiceProvider;
use GuzzleHttp\Exception\ClientException;
use Laravel\Socialite\Two\InvalidStateException;
use League\OAuth1\Client\Credentials\CredentialsException;

/**
 * Поддержка авторизации\регистрации через социальные сети
 *
 * @package FastDog\User\Http\Controllers\Site
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class SocialController extends Controller
{
    /**
     * Доступные провайдеры
     *
     * @var array
     */
    protected $providers = [
        'facebook' => FacebookServiceProvider::class,
        'google' => GoogleServiceProvider::class,
        'vkontakte' => VkontakteServiceProvider::class,
        'twitter' => TwitterServiceProvider::class,
    ];

    /**
     * SocialController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     *  Redirect the user to provider authentication page
     *
     * @param  string $driver
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($driver)
    {
        return (new $this->providers[$driver])->redirect();
    }

    /**
     *  Handle provider response
     *
     * @param  string $driver
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($driver)
    {

        try {
            return (new $this->providers[$driver])->handle();
        } catch (InvalidStateException $e) {
            return $this->redirectToProvider($driver);
        } catch (ClientException $e) {
            return $this->redirectToProvider($driver);
        } catch (CredentialsException $e) {
            return $this->redirectToProvider($driver);
        }
    }
}