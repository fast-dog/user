<?php
/**
 * Created by PhpStorm.
 * User: dg-67
 * Date: 07.02.2017
 * Time: 12:44
 *
 * @var $user \App\Modules\Users\User
 */

?>
@extends('public.000.layouts.user_cabinet')

@section('user_content')
    <div class="profile-body">
        @if(\Session::has('message'))
            <div class="alert alert-success fade in">
                {{\Session::get('message')}}
            </div>
        @endif
        <div class="profile-bio">
            <div class="row">
                <div class="col-md-5">
                    <img class="img-responsive md-margin-bottom-10" src="<?=$user->getPhoto()?>" alt=""
                         style="margin: 0 auto">
                    <a class="btn-u btn-u-sm" href="{{url('cabinet/settings')}}">
                        @trans(Изменить фото)
                    </a>
                </div>
                <div class="col-md-7">
                    <h2><?=$user->getName() ?></h2>
                    <hr>
                    @if(isset($user->profile->data->about) && $user->profile->data->about!== '')
                        <p>{{$user->profile->data->about}}</p>
                    @else
                        <div class="alert alert-warning fade in">
                            @trans(Вы еще не заполнили информацию о себе.)
                        </div>
                    @endif
                </div>
            </div>
        </div><!--/end row-->
    </div>
@endsection