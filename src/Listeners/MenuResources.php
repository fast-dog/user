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

        /**
         * @var $moduleManager ModuleManager
         */
        $moduleManager = \App::make(ModuleManager::class);

        $user = $moduleManager->getInstance('user');

        dd($user);

        if (config('app.debug')) {

        }

        $event->setData($data);
    }
}
