<?php

namespace FastDog\User\Models\Services;

/**
 * Базовая реализация сервиса подписки
 *
 * @package FastDog\User\Models\Services
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class Service implements ServiceInterface
{
    /**
     * @var $user_id int
     */
    protected $user_id;

    /**
     * Авторизация пользователя в логике сервиса
     *
     * @param $user_id int идентификатор пользователя
     * @return boolean
     */
    public function auth($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * Метод проверяет доступность сервися пользователю
     *
     * @return boolean
     */
    public function can()
    {
        return false;
    }

    /**
     * Зависимотсти сервиса от внешних факторов,
     * используются в логике проверок
     *
     * @return mixed|array
     */
    public function dependency()
    {
        return [];
    }
}