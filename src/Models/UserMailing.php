<?php

namespace FastDog\User\Models;


use FastDog\Core\Models\BaseModel;
use FastDog\Core\Models\Domain;
use FastDog\Core\Table\Filters\BaseFilter;
use FastDog\Core\Table\Filters\Operator\BaseOperator;
use FastDog\Core\Table\Interfaces\TableModelInterface;
use FastDog\User\Events\UserMailingAdminPrepare;
use Carbon\Carbon;

/**
 * Реализация простой рассылки по базе подписчиков на сайте
 *
 * @package FastDog\User\Models
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserMailing extends BaseModel implements TableModelInterface
{
    /**
     * Текст сообщения
     * @const string
     */
    const TEXT = 'text';

    /**
     * Состояние рассылки
     * @const string
     */
    const STATE = 'state';

    /**
     * Тема сообщения
     * @const string
     */
    const SUBJECT = 'subject';

    /**
     * Дата начала рассылки
     * @const string
     */
    const START_AT = 'start_at';

    /**
     * Шаблон рассылки
     * @const string
     */
    const TEMPLATE_ID = 'template_id';

    /**
     * Состояние: по умолчанию
     * @const int
     */
    const STATE_DEFAULT = 0;

    /**
     * Состояние: готово к рассылке
     * @const int
     */
    const STATE_READY = 1;

    /**
     * Состояние: в работе
     * @const int
     */
    const STATE_WORK = 2;

    /**
     * Состояние: ошибка
     * @const int
     */
    const STATE_ERROR = 3;

    /**
     * Состояние: завершено успешно
     * @const int
     */
    const STATE_FINISH = 4;

    /**
     * @var string $table
     */
    public $table = 'users_mailing';

    /**
     * @var array $fillable
     */
    public $fillable = [self::NAME, self::SUBJECT, self::TEXT, self::STATE, self::SITE_ID, self::TEMPLATE_ID];

    /**
     * @var array $dates
     */
    public $dates = [self::START_AT];

    /**
     * Возвращает возможные состояния
     *
     * @return array
     */
    public static function getStatusList(): array
    {
        return [
            ['id' => self::STATE_DEFAULT, 'name' => 'По умолчанию'],
            ['id' => self::STATE_READY, 'name' => 'Ожидание выполнения'],
            ['id' => self::STATE_WORK, 'name' => 'В работе'],
            ['id' => self::STATE_ERROR, 'name' => 'Прервана с ошибкой'],
            ['id' => self::STATE_FINISH, 'name' => 'Успешно завершена'],
        ];
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return [
            'id' => $this->id,
            self::NAME => $this->{self::NAME},
            self::TEXT => $this->{self::TEXT},
            self::SUBJECT => $this->{self::SUBJECT},
            self::STATE => $this->{self::STATE},
            self::SITE_ID => $this->{self::SITE_ID},
            self::TEMPLATE_ID => $this->{self::TEMPLATE_ID},
            self::CREATED_AT => ($this->{self::CREATED_AT}) ?
                $this->{self::CREATED_AT}->format(Carbon::DEFAULT_TO_STRING_FORMAT) : '',
            self::START_AT => ($this->{self::START_AT}) ?
                $this->{self::START_AT}->format(Carbon::DEFAULT_TO_STRING_FORMAT) : '',
        ];
    }

    /**
     * Возвращает имя события вызываемого при обработке данных при передаче на клиент в разделе администрирования
     * @return string
     */
    public function getEventAdminPrepareName(): string
    {
        return UserMailingAdminPrepare::class;
    }

    /**
     * Возвращает описание доступных полей для вывода в колонки...
     *
     * ... метод используется для первоначального конфигурирования таблицы,
     * дальнейшие типы, порядок колонок и т.д. будут храниться в обхекте BaseTable
     *
     * @return array
     */
    public function getTableCols(): array
    {
        return [
            [
                'name' => 'Название',
                'key' => self::NAME,
                'domain' => true,
                'link' => 'mailing_item',
            ],
            [
                'name' => trans('app.Дата регистрации'),
                'key' => self::CREATED_AT,
                'width' => 150,
                'link' => null,
                'class' => 'text-center',
            ],
            [
                'name' => trans('app.Домен'),
                'key' => self::SITE_ID,
                'width' => 150,
                'link' => null,
                'class' => 'text-center',
                'related' => 'domain:' . Domain::NAME,
            ],
            [
                'name' => '#',
                'key' => 'id',
                'link' => null,
                'width' => 80,
                'class' => 'text-center',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getAdminFilters(): array
    {
        $default = [
            [
                [
                    BaseFilter::NAME => User::EMAIL,
                    BaseFilter::PLACEHOLDER => 'Email',
                    BaseFilter::TYPE => BaseFilter::TYPE_TEXT,
                    BaseFilter::DISPLAY => false,
                    BaseFilter::OPERATOR => (new BaseOperator('LIKE', 'LIKE'))->getOperator(),
                ],
            ],
            [
                BaseFilter::getLogicAnd(),
                [
                    BaseFilter::TYPE => BaseFilter::TYPE_DATETIME,
                    BaseFilter::NAME => User::CREATED_AT,
                    BaseFilter::DISPLAY => true,
                    BaseFilter::PLACEHOLDER => trans('app.Дата регистрации'),
                    BaseFilter::OPERATOR => (new BaseOperator('BETWEEN', 'BETWEEN'))->getOperator(
                        [['id' => 'BETWEEN', 'name' => 'BETWEEN']]
                    ),
                ],
                BaseFilter::getLogicAnd(),
                [
                    BaseFilter::TYPE => BaseFilter::TYPE_SELECT,
                    BaseFilter::NAME => User::STATUS,
                    BaseFilter::DISPLAY => true,
                    BaseFilter::PLACEHOLDER => trans('app.Состояние'),
                    BaseFilter::DATA => User::getStatusList(),
                    BaseFilter::OPERATOR => (new BaseOperator())->getOperator(),
                ],
            ],
        ];

        return $default;
    }


    /**
     * @return int
     */
    public function getTemplateId(): int
    {
        return 1;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        $template = UserMailingTemplates::where([
            'id' => $this->getTemplateId(),
        ])->first();
        if ($template) {
            return $template;
        }

        return '{TEXT}';
    }
}