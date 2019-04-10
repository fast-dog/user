<?php

namespace FastDog\User\Request;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Регистрация
 *
 * @package FastDog\User\Request
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class Registration extends FormRequest
{
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
        return [
            'email' => 'required|email|unique:users',
            'accept_rules' => 'required',
            'g-recaptcha-response' => 'required|captcha',
            'password' => 'required|min:6|confirmed',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'accept_rules.required' => trans('app.Вы должны принять "Условия использования ресурса"'),
            'g-recaptcha-response.required' => trans('public.The g-recaptcha-response field is required'),
        ];
    }
}
