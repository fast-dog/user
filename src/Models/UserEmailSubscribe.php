<?php
namespace FastDog\User\Models;


use Carbon\Carbon;
use FastDog\Core\Models\BaseModel;
use FastDog\Core\Models\Domain;
use FastDog\Core\Models\DomainManager;
use FastDog\Core\Table\Filters\BaseFilter;
use FastDog\Core\Table\Filters\Operator\BaseOperator;
use FastDog\Core\Table\Interfaces\TableModelInterface;

/**
 * Подписки пользователя
 *
 * @package FastDog\User\Models
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserEmailSubscribe extends BaseModel implements TableModelInterface
{
    /**
     * @const string
     */
    const EMAIL = 'email';

    /**
     * @const string
     */
    const HASH = 'hash';

    /**
     * @var string
     */
    public $table = 'users_email_subscribe';

    /**
     * @var array
     */
    public $fillable = [self::EMAIL, self::SITE_ID, self::HASH];

    /**
     * @return array
     */
    public function getData(): array
    {
        return [
            'id' => $this->id,
            self::EMAIL => $this->{self::EMAIL},
            self::CREATED_AT => $this->{self::CREATED_AT}->format(Carbon::DEFAULT_TO_STRING_FORMAT),
        ];
    }

    /**
     * Возвращает имя события вызываемого при обработке данных при передаче на клиент в разделе администрирования
     * @return string
     */
    public function getEventAdminPrepareName(): string
    {
        return null;// UserAdminPrepare::class;
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
                'name' => 'Email',
                'key' => User::EMAIL,
                'domain' => true,
                'link' => 'user_profile',
            ],
            [
                'name' => trans('app.Дата регистрации'),
                'key' => User::CREATED_AT,
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
     * Возвращает ключ доступа к ACL
     * @param string $type
     * @return string
     */
    public function getAccessKey($type = 'guest'): string
    {
        return strtolower(\FastDog\User\User::class) . '::' . DomainManager::getSiteId() . '::' . $type;
    }

}