<?php

namespace FastDog\User\Models\Desktop;

use FastDog\Core\Interfaces\DesktopWidget;
use FastDog\User\Models\UserRegisterStatistic;

/**
 * Блок графика
 *
 * Блок графика в разделе администрирования
 *
 * @package FastDog\User\Models\Desktop
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class RegisterGraph implements DesktopWidget
{
    /**
     * Параметры модуля
     *
     * @var array|object $config
     */
    protected $config;

    /**
     * Возвращает набор данных для отображения в блоке
     *
     * @return mixed
     */
    public function getData(): array
    {
        return UserRegisterStatistic::getStatistic();
    }

    /**
     * Устанавливает набор данных в контексте объекта
     *
     * @param array $data
     * @return mixed
     */
    public function setData(array $data): void
    {
        $this->config = $data;
    }
}