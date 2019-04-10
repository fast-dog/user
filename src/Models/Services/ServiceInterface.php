<?php

namespace FastDog\User\Models\Services;

use FastDog\User\Models\User;

/**
 * Серсисы для ограничения\предоставления возможностей скрипта
 *
 * @package FastDog\User\Models\Services
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
interface ServiceInterface
{
    /**
     * Авторизация пользователя в логике сервиса
     *
     * @param $user User пользовател
     * @return boolean
     */
    public function auth($user);

    /**
     * Метод проверяет доступность сервися пользователю
     *
     * @return boolean
     */
    public function can();

    /**
     * Зависимотсти сервиса от внешних факторов,
     * используются в логике проверок
     *
     * @return mixed|array
     */
    public function dependency();
}