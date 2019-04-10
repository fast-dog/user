<?php

namespace FastDog\User\Request;

use FastDog\User\Models\User;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Обновление профиля
 *
 * @package FastDog\User\Request
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UpdateProfile extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return (\Auth::guest() === false);
    }

    /**
     * @return array
     */
    public function rules()
    {
        /**
         * @var $user User
         */
        $user = \Auth::getUser();
        $result = [
            'email' => 'required|email',
            'profile.data.about' => 'max:300',

        ];
        if (\Request::input('email') !== $user->{User::EMAIL}) {
            $result['email'] .= '|unique:users';
        }
        if (\Request::input('login') !== $user->{User::LOGIN}) {
            $result['login'] = 'unique:users|max:50';
        }

        return $result;
    }


}