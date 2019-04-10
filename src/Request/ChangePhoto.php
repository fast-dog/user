<?php

namespace FastDog\User\Request;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Изменение аватара-фото
 *
 * @package FastDog\User\Request
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class ChangePhoto extends FormRequest
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
        $result = [
            'photo' => 'required|mimes:jpeg,jpg,png|max:1000',
        ];

        return $result;
    }
}