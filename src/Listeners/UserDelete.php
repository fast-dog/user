<?php

namespace FastDog\User\Listeners;


use FastDog\User\Events\UserUpdate;
use FastDog\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Обновление профиля
 *
 * Событие вызывается перед обновлением профиля, проверяет и корректирует набор переданных в модель данных
 *
 * @package FastDog\User\Listeners
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserDelete
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * UpdateProfile constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Обработчик
     *
     * @param \FastDog\User\Events\UserDelete|UserUpdate $event
     * @return void
     */
    public function handle(\FastDog\User\Events\UserDelete $event)
    {
        /**
         * @var $user User
         */
        $user = $event->getUser();

        switch ($user->type) {
            case User::USER_TYPE_USER:
                /**
                 * @var $items Collection
                 */
//                $items = CatalogItems::where([
//                    CatalogItems::USER_ID => $user->id,
//                    //CatalogItems::STATE => CatalogItems::STATE_PUBLISHED
//                ])->get();
//                $items->each(function (CatalogItems $item, $idx) {
//                    \DB::statement("CALL deleteCatalogItem({$item->id})");
//                    $item->flushCache();
//                });
//                UserReviews::where([
//                    UserReviews::AUTHOR_ID => $user->id
//                ])->delete();

                break;
        }
    }
}
