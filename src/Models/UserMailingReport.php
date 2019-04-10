<?php

namespace FastDog\User\Models;


use FastDog\Core\Models\BaseModel;

/**
 * Рассылки - проведенные рассылки
 *
 * @package FastDog\User\Models
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserMailingReport extends BaseModel
{
    /**
     * @const string
     */
    const TEMPLATE_ID = 'template_id';

    /**
     * @const string
     */
    const USER_ID = 'user_id';

    /**
     * @const string
     */
    const MAILING_ID = 'mailing_id';

    /**
     * @const string
     */
    const PROCESS_ID = 'process_id';

    /**
     * Состояние: Готово к рассылке
     * @const string
     */
    const STATE_READY = 0;

    /**
     * Состояние: Идет рассылка
     * @const string
     */
    const STATE_PROCESS = 1;

    /**
     * Состояние: Рассылка завершена
     * @const string
     */
    const STATE_SUCCESS = 2;

    /**
     * Состояние: Завершено с ошибками
     * @const string
     */
    const STATE_ERRORS = 3;

    /**
     * Состояние: В архиве
     * @const string
     */
    const STATE_ARCHIVE = 4;

    /**
     * @var string $table
     */
    public $table = 'users_mailing_report';

    /**
     * @var array $fillable
     */
    public $fillable = [self::TEMPLATE_ID, self::USER_ID, self::MAILING_ID, self::DATA, self::PROCESS_ID];

    /**
     * @return array
     */
    public static function getStatusList(): array
    {
        return [
            ['id' => self::STATE_READY, 'name' => 'Готово к рассылке'],
            ['id' => self::STATE_PROCESS, 'name' => 'Идет рассылка'],
            ['id' => self::STATE_SUCCESS, 'name' => 'Рассылка завершена'],
            ['id' => self::STATE_ERRORS, 'name' => 'Завершено с ошибками'],
            ['id' => self::STATE_ARCHIVE, 'name' => 'В архиве'],
        ];
    }
}