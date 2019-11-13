<?php

namespace FastDog\User\Http\Request;

use FastDog\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Добавление пользователя
 *
 * @package FastDog\User\Http\Request
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class AddUser extends FormRequest
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
        if (!$this->has('ids')) {
            return [
                'email' => 'required|email|unique:users',
                'type' => 'required',
            ];
        }

        return [];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'email.required' => 'Поле "Контактное лицо" обязательно для заполнения.',
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

            if (!$this->has('id') && !$this->has('ids')) {
                $check = User::where(function (Builder $query) use ($input) {
                    $query->where('email', $input['email']);
                })->first();
                if ($check) {
                    $validator->errors()->add('email', 'Данный email зарегистрирован...');
                }

                if (empty($input['password'])) {
                    $validator->errors()->add('email', 'Пароль не может быть пустым...');
                }
                if (empty($input['site_id'])) {
                    $validator->errors()->add('email', 'Не выбран Уровень доступа...');
                }
            }
        });

        return $validator;
    }
}
