<?php


namespace FastDog\User\Models\View;


use FastDog\User\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Избранные пользователи
 *
 * @package Modules\Users\Models\View
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserFavorites extends User
{
    /**
     * Идентификатор пользователя
     * @const string
     */
    const OWNER_ID = 'owner_id';

    /**
     * Название таблицы
     * @var string $table
     */
    public $table = 'user_users_favorites';

    /**
     * @param $user_id
     * @param int $limit
     * @return mixed
     */
    public static function getFavorites($user_id, $limit = 9)
    {
        return self::where(function (Builder $query) use ($user_id) {
            $query->where(self::OWNER_ID, $user_id);
        })->paginate($limit);
    }
}