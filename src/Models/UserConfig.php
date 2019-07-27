<?php

namespace FastDog\User\Models;


use FastDog\Admin\Models\Desktop;
use FastDog\Core\Models\BaseModel;

/**
 * Парамтеры
 *
 * Параметры административного и публичного разделов
 *
 * @package FastDog\User\Models
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserConfig extends BaseModel
{
    /**
     * Парметры рабочего стола
     *
     * @const string
     */
    const CONFIG_DESKTOP = 'desktop';
    /**
     * Параметры публичного раздела
     *
     * @const string
     */
    const CONFIG_PUBLIC = 'public';

    /**
     * Значение
     * @const string
     */
    const VALUE = 'value';
    /**
     * Название таблицы
     *
     * @var string $table
     */
    public $table = 'users_config';

    /**
     * @var array $fillable
     */
    public $fillable = [self::NAME, self::VALUE, self::ALIAS];

    /**
     * Все параметры
     *
     * Возвращает массив всех параметров модуля для построения таблицы в разделе администрирования
     *
     * @return array
     */
    public static function getAllConfig()
    {
        $result = [];
        self::orderBy('priority')->get()->each(function(self $item) use (&$result) {
            $data = json_decode($item->{'value'});
            if ($item->{self::ALIAS} == self::CONFIG_DESKTOP) {
                foreach ($data as $key => &$value) {
                    $_item = Desktop::where(Desktop::NAME, $value->name)->withTrashed()->first();
                    if ($_item) {
                        $value->value = ($_item->deleted_at === null) ? 'Y' : 'N';
                    }
                }
            }
            $result[$item->alias] = [
                'open' => ($item->alias == \Request::input('open_section', self::CONFIG_DESKTOP)),
                'name' => $item->{self::NAME},
                'config' => $data,
            ];
        });

        return (array)$result;
    }

    /**
     * Подробные данные по модели
     *
     * @return array
     */
    public function getData(): array
    {
        if (is_string($this->{self::VALUE})) {
            $this->{self::VALUE} = json_decode($this->{self::VALUE});
        }
        $result = [
            'id' => $this->id,
            self::NAME => $this->{self::NAME},
            self::ALIAS => $this->{self::ALIAS},
            self::VALUE => $this->{self::VALUE},
        ];

        return $result;
    }

    /**
     * Проверка доступа по ключу
     *
     * @param $access_name
     * @return bool
     */
    public function can($access_name)
    {
        $data = $this->getData();

        foreach ($data[self::VALUE] as $item) {
            if ($item->{'alias'} === $access_name) {
                switch ($item->{'type'}) {
                    case 'select':
                        return ($item->{'value'} === 'Y');
                }
            }
        }

        return false;
    }

}
