<?php

namespace FastDog\User\Models;


use FastDog\Core\Models\BaseModel;

/**
 * Class UserMailingProcess
 * @package FastDog\User\Models
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserMailingProcess extends BaseModel
{
    /**
     * @const string
     */
    const MAILING_ID = 'mailing_id';

    /**
     * @const string
     */
    const CURRENT_STEP = 'current_step';

    /**
     * Состояние: Готово к отправке
     * @const string
     */
    const STATE_READY = 0;

    /**
     * Состояние: Выполняется отправка
     * @const string
     */
    const STATE_PROCESS = 1;

    /**
     * Состояние: Отправка завершена
     * @const int
     */
    const STATE_FINISH = 2;

    /**
     * Состояние: Отправка завершена с ошибками
     * @const int
     */
    const STATE_ERROR = 3;

    /**
     * @var string $table
     */
    public $table = 'users_mailing_process';
    /**
     * @var array $fillable
     */
    public $fillable = [self::MAILING_ID, self::CURRENT_STEP, self::STATE];

    /**
     * @return array
     */
    public static function getStatusList(): array
    {
        return [
            ['id' => self::STATE_READY, 'name' => trans('user::mailing.status_list.ready')],
            ['id' => self::STATE_PROCESS, 'name' => trans('user::mailing.status_list.process')],
            ['id' => self::STATE_FINISH, 'name' => trans('user::mailing.status_list.finish')],
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mailing()
    {
        return $this->hasOne(UserMailing::class, 'id', self::MAILING_ID);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $send_mail = UserMailingReport::where([
            UserMailingReport::PROCESS_ID => $this->id,
        ])->count();
        $total_mail = UserEmailSubscribe::where([
            UserEmailSubscribe::SITE_ID => $this->mailing->site_id,
        ])->count();
        $result = [
            'id' => $this->id,
            self::STATE => $this->{self::STATE},
            self::CURRENT_STEP => $this->{self::CURRENT_STEP},
            'mailing' => $this->mailing->getData(),
            'send_mail' => $send_mail,
            'total_mail' => $total_mail,
            'percent' => ($total_mail > 0) ? ($send_mail * 100) / $total_mail : 0,
        ];

        return $result;
    }
}