<?php

namespace FastDog\User\Social;


use FastDog\User\Models\Profile\UserProfile;
use FastDog\User\Models\UserConfig;
use FastDog\User\User;

/**
 * Авторизация\регистрация через Twitter
 *
 * @package FastDog\User\Social
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class TwitterServiceProvider extends AbstractServiceProvider
{
    /**
     *  Handle Facebook response
     *
     * @return \Illuminate\Http\Response
     */
    public function handle()
    {

        $user = $this->provider->user();

        $existingUser = User::whereEmail($user->email)->orWhere('data->twitter_id', $user->id)->first();

        if ($existingUser) {
            $data = json_decode($existingUser->{User::DATA});

            if (!isset($data->twitter_id)) {
                $data->twitter_id = $user->id;
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
            UserProfile::NAME => $user->name,
            UserProfile::SURNAME => '',
        ]);

        $newUser = $this->register([
            User::EMAIL => $user->email,
            User::TYPE => User::USER_TYPE_USER,
            User::STATUS => ($config->can('registration_confirm')) ? User::STATUS_ACTIVE : User::STATUS_NOT_CONFIRMED,
            User::PASSWORD => \Hash::make($password),
            User::DATA => [
                'twitter_id' => $user->id,
            ],
        ]);

        return $this->login($newUser);
    }
}