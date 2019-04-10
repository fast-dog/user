<?php

namespace FastDog\User\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Пользователи в избранном
 *
 * @package FastDog\User\Models
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserFavorites extends Model
{
    /**
     * Идентификатор владельца
     * @const string
     */
    const USER_ID = 'user_id';

    /**
     * Идентификатор пользователя
     * @const string
     */
    const ITEM_ID = 'item_id';

    /**
     * @var string $table
     */
    public $table = 'users_favorites';

    /**
     * @var bool $timestamps
     */
    public $timestamps = false;

    /**
     * @var array $fillable
     */
    public $fillable = [self::USER_ID, self::ITEM_ID];

}