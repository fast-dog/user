<?php

namespace FastDog\User\Http\Request;

use FastDog\User\Models\User;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Добавление пользователя
 *
 * @package FastDog\User\Http\Request
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
            'name.required' => trans('user::requests.add_mailing.name_required'),
            'subject.required' => trans('user::requests.add_mailing.subject_required'),
            'text.required' => trans('user::requests.add_mailing.text_required'),
            'start_at.required' => trans('user::requests.add_mailing.start_at_required'),
        ];
    }
}
