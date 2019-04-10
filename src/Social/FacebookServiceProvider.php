<?php

namespace FastDog\User\Social;


use Curl\Curl;
use FastDog\User\Models\Profile\UserProfile;
use FastDog\User\Models\UserConfig;
use FastDog\User\User;


/**
 * Авторизация\регистрация через Facebook
 *
 * @package FastDog\User\Social
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class FacebookServiceProvider extends AbstractServiceProvider
{
    /**
     *  Handle Facebook response
     *
     * @return \Illuminate\Http\Response
     */
    public function handle()
    {
        $user = $this->provider->fields([
            'first_name',
            'last_name',
            'email',
            'gender',
            'verified',
        ])->user();

        $existingUser = User::whereEmail($user->email)->orWhere('data->facebook_id', $user->id)->first();

        if ($existingUser) {
            $data = json_decode($existingUser->{User::DATA});

            if (!isset($data->facebook_id)) {
                $data->facebook_id = $user->id;
                $existingUser->data = json_encode($data);
                $existingUser->save();
            }

            return $this->login($existingUser);
        }

        if (isset($user->avatar_original)) {
            $ext = 'jpg';
            $curl = new Curl();
            $curl->get($user->avatar_original);

            if ($curl->httpStatusCode == 302) {
                $location = $curl->responseHeaders->offsetGet('location');
                $file = DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . md5($location) . '.' . $ext;

                if (copy($location, public_path('upload/images') . $file)) {
                    \Request::merge([
                        'create_avatar' => $file,
                    ]);
                }
            }
        }

        $emptyUser = new User();

        /**
         * @var $config UserConfig
         */
        $config = $emptyUser->getPublicConfig();


        $password = User::quickRandom(10);

        \Request::merge([
            User::PASSWORD => $password,
            User::EMAIL => $user->email,
            UserProfile::NAME => $user->user['first_name'],
            UserProfile::SURNAME => $user->user['last_name'],
        ]);

        $newUser = $this->register([
            User::EMAIL => $user->email,
            User::TYPE => User::USER_TYPE_USER,
            User::STATUS => ($config->can('registration_confirm')) ? User::STATUS_ACTIVE : User::STATUS_NOT_CONFIRMED,
            User::PASSWORD => \Hash::make($password),
            User::DATA => [
                'facebook_id' => $user->id,
            ],
        ]);

        return $this->login($newUser);
    }
}