<?php

namespace FastDog\User\Request;

use FastDog\User\Models\User;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Изменение пароля
 *
 * @package FastDog\User\Request
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class ChangePassword extends FormRequest
{

    /**
     * The URI to redirect to if validation fails.
     *
     * @var string $redirect
     */
    protected $redirect = 'cabinet/settings';

    /**
     * @var string $trans_key
     */
    private $trans_key = 'theme#001/modules/users/cabinet/settings/default';

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
        \Session::flash('tab', 'passwordTab');

        $result = [
            'current_' . User::PASSWORD => 'required',
            'new_' . User::PASSWORD => 'required|min:6|confirmed',
        ];

        return $result;
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
            if (false == \Auth::attempt([
                    User::EMAIL => $user->{User::EMAIL},
                    User::PASSWORD => $this->input('current_' . User::PASSWORD),
                ])
            ) {
                $validator->errors()->add('current_' . User::PASSWORD, trans($this->trans_key . '.Текущий пароль не совпадает с введенным.'));
            }
        });

        return $validator;
    }

    public function attributes()
    {
        $result = [
            'current_' . User::PASSWORD => '"' . trans($this->trans_key . '.Текущий пароль') . '"',
            'new_' . User::PASSWORD => '"' . trans($this->trans_key . '.Новый пароль') . '"',
        ];

        return $result;
    }
}