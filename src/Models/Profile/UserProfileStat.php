<?php
namespace FastDog\User\Models\Profile;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Статистика просмотра профиля пользователя
 *
 * @package FastDog\User\Models\Profile
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserProfileStat extends Model
{
    /**
     * Идентификатор пользователя
     * @const string
     */
    const USER_ID = 'user_id';

    /**
     * Идентификатор гостя
     * @const string
     */
    const GUEST_ID = 'guest_id';

    /**
     * Не использовать поля даты\времени
     * @var bool $timestamps
     */
    public $timestamps = false;

    /**
     * Название таблицы
     * @var string $table
     */
    public $table = 'users_profile_stat';

    /**
     * @var array $fillable
     */
    public $fillable = [self::USER_ID, self::GUEST_ID, self::CREATED_AT];

    /**
     * Записать посещение профиля
     *
     * @param $user_id
     * @param int $guest_id
     * @return bool
     */
    public static function add($user_id, $guest_id = 0)
    {
        if ($user_id != $guest_id) {
            return self::create([
                self::USER_ID => $user_id,
                self::GUEST_ID => $guest_id,
                self::CREATED_AT => Carbon::now()->format(Carbon::DEFAULT_TO_STRING_FORMAT),
            ]);
        }

        return false;
    }
}