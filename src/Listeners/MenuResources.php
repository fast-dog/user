<?php

namespace FastDog\User\Listeners;

use FastDog\Core\Models\ModuleManager;
use Illuminate\Http\Request;

/**
 *
 * @package FastDog\User\Listeners
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class MenuResources
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
     * @param \FastDog\Menu\Events\MenuResources $event
     */
    public function handle(\FastDog\Menu\Events\MenuResources $event)
    {

        $data = $event->getData();

        if (!$data['resource']) {
            $data['resource'] = [];
        }
        $data['resource']['user'] = [
            'id' => 'user',
            'name' => trans('user::interface.Пользователи'),
            'items' => collect([])
        ];
        /**
         * @var $moduleManager ModuleManager
         */
        $moduleManager = \App::make(ModuleManager::class);

        $user = $moduleManager->getInstance('user');

        $items = (isset($user['menu']) && $user['menu'] instanceof \Closure) ? $user['menu']() : collect([]);

        $items->each(function($item) use (&$data) {
            $resource = [
                'id' => $item['id'],
                'name' => $item['name'],
                'sort' => (int)$item['sort'],
                'data' => [
                    'route_instance' => [
                        'instance' => $item['route_instance']
                    ]
                ]
            ];
            if (isset($item['templates']['000']['templates'][0])) {
                $resource['data']['template'] = [
                    'id' => $item['templates']['000']['templates'][0]['id'],
                    'name' => $item['templates']['000']['templates'][0]['name']
                ];
            }
            $data['resource']['user']['items']->push($resource);
        });
        $data['resource']['user']['items'] = $data['resource']['user']['items']->sortBy('sort');
        if (config('app.debug')) {
            $data['_events_'][] = __METHOD__;
        }

        $event->setData($data);
    }
}
