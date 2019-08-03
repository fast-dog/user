<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 27.01.2017
 * Time: 1:57
 */
?>
@extends('emails.users.layout.main')

@section('title')
    Регистрационная информация
@stop

@section('content')
    <h4>Регистрация завершена</h4>
@stop
@section('content2')
    <p>Ваш логин: <strong><?=$email?></strong></p>
    <p>Ваш Пароль: <strong><?=$password?></strong></p>
@stop
@section('content3')
@stop
@section('content4')
    <p>Письмо сформировано автоматически, не нужно на него отвечать.</p>
@stop
