<?php

namespace FastDog\User\Request;


use FastDog\User\Models\User;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Добавление пользователя
 *
 * @package FastDog\User\Request
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class AddMailing extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        \Auth::check();
        if (!\Auth::guest() && \Auth::getUser()->type == User::USER_TYPE_ADMIN) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'subject' => 'required',
            'text' => 'required',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Поле "Название" обязательно для заполнения.',
            'subject.required' => 'Поле "Тема" обязательно для заполнения.',
            'text.required' => 'Поле "HTML Текст" обязательно для заполнения.',
        ];
    }

    /**
     * @return \Illuminate\Contracts\Validation\Validator|mixed
     */
    public function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance();
//        $validator->after(function () use ($validator) {
//            $input = $this->all();
//            if (!$input['id']) {
//                $check = User::where(function ($query) use ($input) {
//                    $query->where('email', $input['email']);
//                })->first();
//                if ($check) {
//                    $validator->errors()->add('email', 'Данный email зарегистрирован...');
//                }
//
//                if (empty($input['password'])) {
//                    $validator->errors()->add('email', 'Пароль не может быть пустым...');
//                }
//                if (empty($input['site_id'])) {
//                    $validator->errors()->add('email', 'Не выбран Уровень доступа...');
//                }
//            }
//        });

        return $validator;
    }
}
