<?php

namespace FastDog\User\Models;


use FastDog\Core\Models\BaseModel;
use FastDog\Core\Models\DomainManager;
use FastDog\Core\Properties\BaseProperties;
use FastDog\Core\Table\Filters\BaseFilter;
use FastDog\Core\Table\Filters\Operator\BaseOperator;
use FastDog\Core\Table\Interfaces\TableModelInterface;
use FastDog\User\Events\UserMailingTemplatesAdminPrepare;
use Illuminate\Support\Collection;

/**
 * Рассылки - шаблоны рассылок
 *
 * @package FastDog\User\Models
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserMailingTemplates extends BaseModel implements TableModelInterface
{
    /**
     * Текст шаблона
     *
     * @const string
     */
    const TEXT = 'text';

    /**
     * @var string $table
     */
    public $table = 'users_mailing_templates';

    /**
     * @var array $fillable
     */
    public $fillable = [self::NAME, self::STATE, self::SITE_ID, self::TEXT];

    /**
     * Возвращает имя события вызываемого при обработке данных при передаче на клиент в разделе администрирования
     * @return string
     */
    public function getEventAdminPrepareName(): string
    {
        return UserMailingTemplatesAdminPrepare::class;
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
                'link' => 'mailing_templates_item',
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
                    BaseFilter::NAME => \FastDog\User\Models\UserMailingTemplates::NAME,
                    BaseFilter::PLACEHOLDER => trans('user::forms.templates.name'),
                    BaseFilter::TYPE => BaseFilter::TYPE_TEXT,
                    BaseFilter::DISPLAY => true,
                    BaseFilter::OPERATOR => (new BaseOperator('LIKE', 'LIKE'))->getOperator(),
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

    /**
     * @return array
     */
    public function getData(): array
    {
        $result = parent::getData();

        $result[self::TEXT] = $this->{self::TEXT};

        return $result;
    }

    /**
     * @return Collection
     */
    public function getDefaultProperties(): Collection
    {
        $result = [
            [
                BaseProperties::NAME => 'Email отправителя',
                BaseProperties::ALIAS => 'FROM_ADDRESS',
                BaseProperties::VALUE => '',
                BaseProperties::SORT => 100,
                BaseProperties::TYPE => BaseProperties::TYPE_STRING,
                BaseProperties::DATA => json_encode([
                    'description' => 'Email адрес отправителья письма',
                ]),
            ],
            [
                BaseProperties::NAME => 'Отправитель письма',
                BaseProperties::ALIAS => 'FROM_NAME',
                BaseProperties::VALUE => '',
                BaseProperties::SORT => 100,
                BaseProperties::TYPE => BaseProperties::TYPE_STRING,
                BaseProperties::DATA => json_encode([
                    'description' => 'Отправитель письма',
                ]),
            ],
        ];

        return collect($result);
    }

    /**
     * @return array
     */
    public static function getList()
    {
        $result = [];

        self::where([
            self::STATE => self::STATE_PUBLISHED,
        ])->get()->each(function (self $item) use (&$result) {
            array_push($result, $item->getData());
        });

        return $result;
    }
}