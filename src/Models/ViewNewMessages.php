<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 09.05.2017
 * Time: 11:12
 */

namespace FastDog\User\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Выборка новых сообщений
 *
 * @package FastDog\User\Models
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class ViewNewMessages extends Model
{
    public $table = 'new_messages';
}