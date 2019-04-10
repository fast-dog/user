<?php

namespace FastDog\User\Request;

use FastDog\User\User;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Добавление изображений к сообщению чата
 *
 * @package FastDog\User\Request
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class AddAttach extends FormRequest
{
    /**
     * Максимальное кол-во записей в базе
     *
     * @var int $count
     */
    private $count = 1;

    /**
     * @return bool
     */
    public function authorize()
    {
        \Auth::check();
        if (!\Auth::guest()) {
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
            'attach' => 'mimes:jpeg,jpg,png,gif|required|max:10000',
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
            /**
             * @var $user User
             */
            $user = \Auth::getUser();
            $count = GalleryItem::where([
                GalleryItem::USER_ID => $user->id,
                GalleryItem::PARENT_TYPE => GalleryItem::TYPE_CHAT_MESSAGE,
                GalleryItem::PARENT_ID => 0,
            ])->count();
            if ($this->count < $count) {
                $validator->errors()->add('attach', trans('public.Добавлено максимальное кол-во вложений.'));
            }
        });

        return $validator;
    }
}
