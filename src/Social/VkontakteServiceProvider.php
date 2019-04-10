<?php
namespace FastDog\User\Social;



use FastDog\User\Models\Profile\UserProfile;
use FastDog\User\Models\UserConfig;
use FastDog\User\User;

/**
 * Авторизация\регистрация через Vk
 *
 * @package FastDog\User\Social
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class VkontakteServiceProvider extends AbstractServiceProvider
{
    /**
     *  Handle Facebook response
     *
     * @return \Illuminate\Http\Response
     */
    public function handle()
    {

        $user = $this->provider->user();

        $existingUser = User::whereEmail($user->email)->orWhere('data->vk_id', $user->id)->first();

        if ($existingUser) {
            $data = json_decode($existingUser->{User::DATA});

            if (!isset($data->vk_id)) {
                $data->vk_id = $user->id;
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
            UserProfile::NAME => (isset($user->user['first_name'])) ? $user->user['first_name'] : $user->name,
            UserProfile::SURNAME => (isset($user->user['last_name'])) ? $user->user['last_name'] : '',
        ]);

        $newUser = $this->register([
            User::EMAIL => $user->email,
            User::TYPE => User::USER_TYPE_USER,
            User::STATUS => ($config->can('registration_confirm')) ? User::STATUS_ACTIVE : User::STATUS_NOT_CONFIRMED,
            User::PASSWORD => \Hash::make($password),
            User::DATA => [
                'vk_id' => $user->id,
            ],
        ]);

        return $this->login($newUser);
    }
}