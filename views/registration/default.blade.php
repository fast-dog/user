<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 06.02.2017
 * Time: 23:27
 */

$faker = Faker\Factory::create();
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
            <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
                <form class="reg-page" method="post" action="<?=url('registration', [], config('app.use_ssl'))?>">
                    <div class="reg-header">
                        <h2>Регистрация нового аккаунта</h2>
                        <p>Уже зарегистрированы? Пройдите
                            <a href="<?=url('login', [], config('app.use_ssl'))?>" class="color-green">авторизация</a>
                            для входа в Ваш аккаунт.</p>
                    </div>

                    <label>Имя</label>
                    <input type="text" name="name" class="form-control margin-bottom-20"
                           value="<?=$faker->firstName?>">

                    <label>Фамилия</label>
                    <input type="text" name="surname" class="form-control margin-bottom-20"
                           value="<?=$faker->lastName?>">

                    <label>Email<span class="color-red">*</span></label>
                    <input type="text" name="email" class="form-control margin-bottom-20"
                           value="test_<?=$faker->email?>">
                    @if ($errors->has('email'))
                        <div class="alert alert-danger fade in">
                            {{ $errors->first('email') }}
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Пароль <span class="color-red">*</span></label>
                            <input type="text" name="password" class="form-control margin-bottom-20"
                                   value="qwerty">
                        </div>
                        <div class="col-sm-6">
                            <label>Подтверджение пароля<span class="color-red">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control margin-bottom-20"
                                   value="qwerty">
                        </div>
                        @if ($errors->has('password'))
                            <div class="col-sm-12">
                                <div class="alert alert-danger fade in">
                                    {{ $errors->first('password') }}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row text-center">
                        <div class="col-sm-12">
                            <?= app('captcha')->display() ?>
                            @if ($errors->has('g-recaptcha-response'))
                                <div class="alert alert-danger fade in">
                                    {{ $errors->first('g-recaptcha-response') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-lg-6 checkbox">
                            <label>
                                <input type="checkbox" name="accept_rules">
                                Я согласен(на) с <a href="<?=url('/service/term', [], config('app.use_ssl'))?>"
                                                    class="color-green">
                                    Условиями использования ресурса</a>
                            </label>
                        </div>
                        <div class="col-lg-6 text-right">
                            <button class="btn-u" type="submit">Продолжить</button>
                        </div>
                        @if ($errors->has('accept_rules'))
                            <div class="col-sm-12">
                                <div class="alert alert-danger fade in">
                                    {{ $errors->first('accept_rules') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div><!--/container-->
    <!--=== End Content Part ===-->
@endsection
