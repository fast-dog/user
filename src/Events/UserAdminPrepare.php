<?php

namespace FastDog\User\Events;

use FastDog\User\User;


/**
 * Обработка данных перед редактированием
 *
 * @package FastDog\User\Events
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserAdminPrepare
{

    /**
     * @var array $data
     */
    protected $data = [];

    /**
     * @var User $item
     */
    protected $item;

    /**
     * @var $result array
     */
    protected $result;

    /**
     * MenuItemBeforeSave constructor.
     * @param array $data
     * @param $item
     */
    public function __construct(array &$data, &$item, &$result)
    {
        $this->data = &$data;
        $this->item = &$item;
        $this->result = &$result;
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * @param array $result
     */
    public function setResult(array $result)
    {
        $this->result = $result;
    }

    /**
     * @return User
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $data
     * @return void
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}