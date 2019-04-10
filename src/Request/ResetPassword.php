<?php


namespace FastDog\User\Request;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Сброс пароля
 *
 * @package FastDog\User\Request
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class ResetPassword extends FormRequest
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
            'email' => 'required|email',
            'g-recaptcha-response' => 'required|captcha',
        ];
    }

}