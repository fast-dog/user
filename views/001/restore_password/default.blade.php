<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 07.02.2017
 * Time: 17:29
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
                <form class="reg-page" action="<?=url('/restore-password', [], config('app.use_ssl'))?>" method="post">
                    {{ csrf_field() }}
                    <div class="reg-header">
                        <h2>Восстановление доступа</h2>
                    </div>

                    <div class="input-group margin-bottom-20">
                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                        <input type="text" placeholder="Email"
                               class="form-control"
                               value="{{ old('email') }}"
                               maxlength="60"
                               name="email">
                    </div>
                    @if ($errors->has('email'))
                        <div class="alert alert-danger fade in">
                            {{ $errors->first('email') }}
                        </div>
                    @endif
                    <div class="input-group margin-bottom-20">
                        <?= app('captcha')->display() ?>
                            @if ($errors->has('g-recaptcha-response'))
                                <div class="alert alert-danger fade in">
                                    {{ $errors->first('g-recaptcha-response') }}
                                </div>
                            @endif
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn-u pull-right" type="submit">Продолжить</button>
                        </div>
                    </div>
                    <hr>
                    <h4>Вспомнили пароль ?</h4>
                    <p>Пройдите <a href="<?=url('login', [], config('app.use_ssl'))?>"
                                   class="color-green">авторизацию</a>
                        для входа в Ваш аккаунт.</p>
                </form>
            </div>
        </div><!--/row-->
    </div><!--/container-->
    <!--=== End Content Part ===-->
@endsection
