<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 03.05.2017
 * Time: 22:24
 */

dump($messages)
?>
@extends('public.000.layouts.user_cabinet')

@section('user_content')
    <div class="profile-body">
        <div class="panel panel-profile">
            @if($recipient)
                <div class="panel-heading overflow-h">
                    <h2 class="panel-title heading-sm pull-left"><i class="fa fa-comments"></i>
                        {{trans('public.Новое личное сообщение пользователю :name',['name'=>$recipient->getName()])}}
                    </h2>
                </div>
                <div class="panel-body margin-bottom-50">
                @foreach($messages->messages  as $msg)
                    @include('public.001.modules.users.cabinet_messages.partials.message')
                @endforeach
                <!--<button type="button" class="btn-u btn-u-default btn-block">Load More</button>-->
                </div>
                @include('public.001.modules.users.cabinet_messages.partials.form')
            @else
                <div class="alert alert-info">{{ trans('public.Пользователь не найден.') }}</div>
            @endif
        </div>
    </div>
@endsection
