<?php

namespace FastDog\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Статистика визитов пользователей
 *
 * @package FastDog\User\Models
 * @version 0.1.16
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserVisitStatistic extends Model
{
    /**
     * Название таблицы
     *
     * @var string $table
     */
    protected $table = 'users_visit_stat';

    public $fillable = ['value', self::CREATED_AT];

    /**
     *
     * @return bool
     */
    public function delete()
    {
        return false; //parent::delete();
    }

    /**
     * Получение статистики
     *
     * Метод возвращает 30 последних записей из таблицы статистики с сортировкой по уменьшению времени
     *
     * @param bool $fire_event
     * @return array
     */
    public static function getStatistic($fire_event = true)
    {
        $result = [];
        $items = self::orderBy('created_at', 'asc')->limit(100)->get();
        foreach ($items as $item) {
            $time = ($item->created_at->getTimestamp() * 1000);
            array_push($result, [
                $time, (int)$item->value,
            ]);
        }

        return $result;
    }
}
