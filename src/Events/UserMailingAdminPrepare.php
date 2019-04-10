<?php

namespace FastDog\User\Events;


use FastDog\Core\Interfaces\AdminPrepareEventInterface;
use FastDog\User\Models\UserMailing;

/**
 * Обработка данных перед редактированием
 *
 * @package FastDog\User\Events
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserMailingAdminPrepare implements AdminPrepareEventInterface
{
    /**
     * @var array $data
     */
    protected $data = [];

    /**
     * @var UserMailing $item
     */
    protected $item;
    /**
     * @var $result array
     */
    protected $result;

    /**
     * ContentAdminPrepare constructor.
     * @param array $data
     * @param UserMailing $item
     * @param $result
     */
    public function __construct(array &$data, UserMailing &$item, &$result)
    {
        $this->data = &$data;
        $this->item = &$item;
        $this->result = &$result;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param array $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @return UserMailing
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