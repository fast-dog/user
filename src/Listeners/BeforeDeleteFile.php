<?php

namespace FastDog\User\Listeners;

use Illuminate\Http\Request;
use FastDog\Media\Events\BeforeDeleteFile as EventBeforeDeleteFile;

/**
 * Перед удалением файла
 *
 * Событие вызывается перед удалением файла, в текущей реализации действий не произодится
 *
 * @package FastDog\User\Listeners
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class BeforeDeleteFile
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * BeforeDeleteFile constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Обработчик
     * @param EventBeforeDeleteFile $event
     * @return void
     */
    public function handle(EventBeforeDeleteFile $event)
    {

    }
}
