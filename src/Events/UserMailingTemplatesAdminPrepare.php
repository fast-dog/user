<?php

namespace FastDog\User\Events;


use FastDog\Core\Interfaces\AdminPrepareEventInterface;
use FastDog\User\Models\UserMailingTemplates;
use Illuminate\Database\Eloquent\Model;

/**
 * Обработка данных перед редактированием
 *
 * @package FastDog\User\Events
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserMailingTemplatesAdminPrepare implements AdminPrepareEventInterface
{
    /**
     * @var array $data
     */
    protected $data = [];

    /**
     * @var UserMailingTemplates $item
     */
    protected $item;
    /**
     * @var $result array
     */
    protected $result;

    /**
     * ContentAdminPrepare constructor.
     * @param array $data
     * @param UserMailingTemplates $item
     * @param $result
     */
    public function __construct(array &$data, UserMailingTemplates &$item, &$result)
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
    public function setResult(array $result): void
    {
        $this->result = $result;
    }

    /**
     * @return Model
     */
    public function getItem(): Model
    {
        return $this->item;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param $data
     * @return void
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }
}