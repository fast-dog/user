<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 001 01.07.19
 * Time: 15:21
 */
return [
    'Регистрация пользователя' => 'Регистрация пользователя',
    'text_registration' => <<<HTML
<p>Ваш логин: <strong>{{EMAIL}}</strong></p>
<p>Ваш пароль: <strong>{{PASSWORD}}</strong></p>
HTML
    ,
    'Регистрация пользователя с подтверждением' => 'Регистрация пользователя с подтверждением',
    'text_registration_confirm' => <<<HTML
<p>Ваш новый пароль: <strong>{{PASSWORD}}</strong></p>
<br />
<p>Для активации аккаунта пройдите по <a href="{{CONFIRM_LINK}}">ссылке</a></p>
HTML
    ,
    'Сброс пароля' => 'Сброс пароля',
    'text_password_reset' => <<<HTML
<p>Ваш новый пароль: <strong>{{PASSWORD}}</strong></p>
<br />
<p>Для активации аккаунта пройдите по <a href="{{CONFIRM_LINK}}">ссылке</a></p>
HTML
    ,

];