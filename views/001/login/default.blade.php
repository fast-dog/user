<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 06.02.2017
 * Time: 23:26
 */

?>
@extends('public.000.layouts.default')

@section('css')
    <link rel="stylesheet" href="<?=url($theme . 'css/pages/page_log_reg_v1.css')?>">
@stop

@section('content')
    @module('Цепочка навигации')
    <!--=== Content Part ===-->
    <div class="container content">
        <div class="row">
            @if (Session::has('message'))
                <div class="alert alert-info fade in">
                    {{ Session::get('message') }}
                </div>
            @endif

            <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
                <form class="reg-page" action="<?=url('/login')?>" method="post">
                    {{ csrf_field() }}
                    <div class="reg-header">
                        <h2>Вход в аккаунт</h2>
                        <ul class="social-icons text-center">
                            <li><a class="rounded-x social_facebook" data-original-title="Facebook"
                                   href="<?=url('auth/facebook')?>"></a>
                            </li>
                            <li><a class="rounded-x social_twitter" data-original-title="Twitter"
                                   href="<?=url('auth/twitter')?>"></a>
                            </li>
                            <li><a class="rounded-x social_googleplus" data-original-title="Google Plus"
                                   href="<?=url('auth/google')?>">
                                </a>
                            </li>
                            <li><a class="rounded-x social_vk" data-original-title="VKontakte"
                                   href="<?=url('auth/vkontakte')?>"></a>
                            </li>
                        <!--<li><a class="rounded-x social_instagram" data-original-title="Instagram"
                                   href="<?=url('auth/instagram')?>"></a>
                            </li>-->
                        </ul>
                    </div>
                    @if ($errors->has('email'))
                        <div class="alert alert-danger fade in">
                            {{ $errors->first('email') }}
                        </div>
                    @endif
                    <div class="input-group margin-bottom-20">
                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                        <input type="text" placeholder="Username"
                               class="form-control"
                               value="{{ old('username') }}"
                               maxlength="60"
                               name="username">
                    </div>
                    @if ($errors->has('username'))
                        <div class="alert alert-danger fade in">
                            {{ $errors->first('username') }}
                        </div>
                    @endif
                    <div class="input-group margin-bottom-20">
                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                        <input type="password" placeholder="Password"
                               class="form-control"
                               name="password">
                    </div>
                    @if ($errors->has('password'))
                        <div class="alert alert-danger fade in">
                            {{ $errors->first('password') }}
                        </div>
                    @endif
                    <? if (\Request::input('use_captcha')): ?>
                    <?= app('captcha')->display() ?>
                    <? endif;?>
                    <div class="row">
                        <div class="col-md-6 checkbox">
                            <label><input type="checkbox" name="remember" checked>Запомнить</label>
                        </div>
                        <div class="col-md-6">
                            <button class="btn-u pull-right" type="submit">Авторизация</button>
                        </div>
                    </div>
                    <hr>
                    <h4>Забыли пароль ?</h4>
                    <p>Не волнуйтесь, <a class="color-green" href="<?=url('restore-password')?>">нажмите</a> для
                        восстановления.</p>
                </form>
            </div>
        </div><!--/row-->
    </div><!--/container-->
    <!--=== End Content Part ===-->
@endsection