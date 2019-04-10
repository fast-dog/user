<?php

namespace FastDog\User\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Email сообщения пользователю
 *
 * Реализация обратной связи с пользователем по средствам email
 *
 * @package FastDog\User\Models
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserEmails extends Model
{
    /**
     * Идентификатор пользователя
     *
     * @const int
     */
    const USER_ID = 'user_id';
    /**
     * Текст сообщения
     *
     * @const string
     */
    const MESSAGE = 'text';
    /**
     * Тема сообщения
     *
     * @const string
     */
    const SUBJECT = 'subject';
    /**
     * Название таблицы
     * @var string $table
     */
    public $table = 'users_emails';
    /**
     * Массив полей автозаполнения
     *
     * @var array $fillable
     */
    public $fillable = [self::USER_ID, self::MESSAGE, self::SUBJECT];


    /**
     * Получение списка отправленных писем
     *
     * @param Request $request
     * @param User $user
     * @return array
     */
    public static function getEmailMessageList(Request $request, User $user)
    {
        $field = ($request->sort) ? $request->sort : 'id';
        $direction = ($request->direction) ? $request->direction : 'desc';
        $data = [];

        $items = UserEmails::where(function (Builder $query) use ($request, $user) {
            $query->where(self::USER_ID, $user->id);
            $filter = $request->filter;
            if ($filter) {
                foreach ($filter as $key => $value) {
                    if ($value) {
                        switch ($key) {
                            case 'id':
                                $query->where($key, $value);
                                break;
                        }
                    }
                }
            }
        })->orderBy($field, $direction)->paginate(25);

        $total = $items->total();

        foreach ($items as $item) {
            array_push($data, [
                'id' => $item->id,
                'created_at' => $item->created_at->format('d.m.Y H:i'),
                UserEmails::SUBJECT => $item->{UserEmails::SUBJECT},
            ]);
        }

        return [
            'user_id' => $user->id,
            'total' => $total,
            'current_page' => $request->input('page', 1),
            'pages' => ceil($total / 25),
            'data' => $data,
        ];
    }
}
