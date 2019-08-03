<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 14.08.2017
 * Time: 17:05
 */

?>
<div class="media media-v2">
    <a class="pull-left" href="{{$item['sender_public_link'] }}" target="_blank">
        <img class="media-object rounded-x" src="{{$item['photo']}}" alt="">
    </a>
    <div class="media-body">
        <h4 class="media-heading">
            <strong>
                <a href="{{$item['sender_public_link'] }}" target="_blank">
                    {{$item['user_name']}}
                </a>
            </strong>
            <i class="fa fa-circle {{($item['is_online'])?'text-success':'text-danger'}}"></i>
            <small>{{$item['humans_time']}}</small>
        </h4>
        <p>{!! $item['message'] !!}</p>
        <ul class="list-inline pull-right">
            <li><a href="{{$item['conversation_link']}}"><i class="expand-list rounded-x fa fa-reply"></i></a></li>
        </ul>
    </div>
</div>