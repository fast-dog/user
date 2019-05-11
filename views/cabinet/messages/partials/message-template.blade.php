<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 14.08.2017
 * Time: 17:05
 *
 * @var $messageManager App\Modules\Users\Entity\MessageManager
 * @var $user \App\Modules\Users\Entity\User
 *
 */

?>
<div class="media media-v2 chat-mess" data-action="message-template">
    <a class="pull-left" data-answer="true" data-class="pull-right" data-bind="sender-public-link" href="#"
       target="_blank">
        <img class="media-object rounded-x" data-bind="sender-photo" src="/upload/images/users/.cache-150x/noPhoto.png"
             alt=""></a>
    <div class="media-body">
        <h4 class="media-heading" data-answer="true" data-class="text-right media-heading">
            <a href="#" data-bind="sender-public-link" target="_blank"><strong data-bind="sender-name" ></strong></a>
            <i class="fa fa-circle text-danger" data-bind="online" data-online="fa fa-circle text-success"
               data-offline="text-danger"></i>
            <span data-bind="humans_time"></span>
        </h4>
        <p data-bind="message" data-answer="true" data-class="text-right" ></p>
    </div>
</div>