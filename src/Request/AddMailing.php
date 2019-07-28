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
            'start_at' => 'required',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => trans('user::requests.name_required'),
            'subject.required' => trans('user::requests.subject_required'),
            'text.required' => trans('user::requests.text_required'),
            'start_at.required' => trans('user::requests.start_at_required'),
        ];
    }
}
