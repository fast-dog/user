<?php

namespace FastDog\User\Http\Request;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Автоизация
 *
 * @package FastDog\User\Http\Request
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class Login extends FormRequest
{
    use  ThrottlesLogins;

    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules()
    {
        $result = [
            'username' => 'required|min:5|max:60|email',
            'password' => 'required|min:6',
        ];

        return $result;
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'username' => '"' . trans('public.E-mail/Login') . '"',
            'password' => '"' . trans('public.Password') . '"',
        ];
    }

    /**
     * @return \Illuminate\Contracts\Validation\Validator|mixed
     */
    public function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance();
        $validator->after(function () use ($validator) {
            $input = $this->all();
//            if (isset($input['username'])) {
//                $check = User::where(function ($query) use ($input) {
//                    $query->where(User::EMAIL, $input['username']);
//                })->first();
//                if (!$check) {
//                    $validator->errors()->add('password', trans('public.Auth filed...'));
//                }
//            }

        });

        return $validator;
    }

}