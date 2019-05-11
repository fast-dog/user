<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 14.08.2017
 * Time: 17:05
 *
 * @var $messageManager App\Modules\Users\Entity\MessageManager
 * @var $user \App\Modules\Users\Entity\User
 * @var $message array
 */



?>
<div class="media media-v2 chat-mess" id="message-{{$message['id']}}">
    <a class="{!! ($message['answer'])?'pull-right':'pull-left' !!}"
       data-answer="true"
       data-class="pull-right"
       href="{{$message['sender_public_link']}}" target="_blank">
        <img class="media-object rounded-x" src="{{$message['photo']}}" alt="">
    </a>
    <div class="media-body">
        <h4 class="media-heading" data-answer="true" {!! ($message['answer'])?'style="text-align: right"':'' !!}>
            <strong>
                <a href="{{$message['sender_public_link']}}" target="_blank">
                    {{$message['user_name']}}
                </a>
            </strong>
            <i class="fa fa-circle {{($message['is_online'])?'text-success':'text-danger'}}"></i>
            <span>{{$message['humans_time']}} ({{$message['format_time']}})</span>
        </h4>
        <p data-bind="message" {!! ($message['answer'])?'style="text-align: right"':'' !!}>
            <?=$message['message']?>
        </p>
    </div>
</div>