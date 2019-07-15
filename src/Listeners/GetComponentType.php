<?php
namespace FastDog\User\Listeners;


use FastDog\Core\Models\Components;
use FastDog\User\User;
use Illuminate\Http\Request;
use FastDog\Core\Events\GetComponentType as GetComponentTypeEvent;
use Illuminate\Support\Arr;

/**
 * Class GetComponentType
 * @package FastDog\User\Listeners
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class GetComponentType
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * ContentAdminPrepare constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param GetComponentTypeEvent $event
     */
    public function handle(GetComponentTypeEvent $event)
    {
        $data = $event->getData();

        $paths = Arr::first(config('view.paths'));
        array_push($data, [
            'id' => 'users',
            'instance' => User::class,
            'name' => trans('user::interface.Пользователи'),
            'items' => [
                [
                    'id' => 'messages',
                    'name' => trans('user::interface.Пользователи') . ' :: ' . trans('user::modules.Личные сообщения'),
                    'templates' => Components::getTemplates($paths . '/modules/users/messages/*.blade.php'),
                ],
                [
                    'id' => 'login',
                    'name' => trans('user::interface.Пользователи') . ' :: ' . trans('user::modules.Авторизация пользователя'),
                    'templates' => Components::getTemplates($paths . '/modules/users/auth/*.blade.php'),
                ],
                [
                    'id' => 'registration',
                    'name' => trans('user::interface.Пользователи') . ' :: ' . trans('user::modules.Регистрация пользователя'),
                    'templates' => Components::getTemplates($paths . '/modules/users/registration/*.blade.php'),
                ],
                [
                    'id' => 'subscribe',
                    'name' => trans('user::interface.Пользователи') . ' :: ' . trans('user::modules.Подписка на рассылку'),
                    'templates' => Components::getTemplates($paths . '/modules/users/subscribe/*.blade.php'),
                ],
            ],
        ]);


        if (config('app.debug')) {
            $data['_events'][] = __METHOD__;
        }
        $event->setData($data);
    }
}