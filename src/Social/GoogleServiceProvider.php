<?php

namespace FastDog\User\Social;

use FastDog\User\Models\Profile\UserProfile;
use FastDog\User\Models\UserConfig;
use FastDog\User\User;

/**
 * Авторизация\регистрация через G+
 *
 * @package FastDog\User\Social
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class GoogleServiceProvider extends AbstractServiceProvider
{
    /**
     *  Handle Facebook response
     *
     * @return \Illuminate\Http\Response
     */
    public function handle()
    {

        $user = $this->provider->user();

        $existingUser = User::whereEmail($user->email)->orWhere('data->google_plus_id', $user->id)->first();

        if ($existingUser) {
            $data = json_decode($existingUser->{User::DATA});

            if (!isset($data->google_plus_id)) {
                $data->google_plus_id = $user->id;
                $existingUser->data = json_encode($data);
                $existingUser->save();
            }

            return $this->login($existingUser);
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
            UserProfile::NAME => (isset($user->user['name']['givenName'])) ? $user->user['name']['givenName'] : $user->name,
            UserProfile::SURNAME => (isset($user->user['name']['familyName'])) ? $user->user['name']['familyName'] : '',
        ]);

        if (isset($user->avatar_original)) {
            $file = md5($user->email);
            $ext = array_last(explode('.', $user->avatar_original));
            $ext = (!$ext) ? 'jpg' : $ext;
            $file = DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . $file . '.' . $ext;

            if (copy($user->avatar_original, public_path('upload/images') . $file)) {
                \Request::merge([
                    'create_avatar' => $file,
                ]);
            }
        }

        $newUser = $this->register([
            User::EMAIL => $user->email,
            User::TYPE => User::USER_TYPE_USER,
            User::STATUS => ($config->can('registration_confirm')) ? User::STATUS_ACTIVE : User::STATUS_NOT_CONFIRMED,
            User::PASSWORD => \Hash::make($password),
            User::DATA => [
                'google_plus_id' => $user->id,
            ],
        ]);

        return $this->login($newUser);
    }
}