<?php

$activeTab = \Session::get('tab', 'profile');
?>
@extends('public.000.layouts.user_cabinet')

@section('user_content')
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="profile-body margin-bottom-20">
        <div class="tab-v1">
            <ul class="nav nav-justified nav-tabs">
                <li {!! $activeTab == 'profile'?'class="active"':'' !!}>
                    <a data-toggle="tab" href="#profile">
                        @trans(Личные данные)
                    </a>
                </li>
                <li {!! $activeTab == 'passwordTab'?'class="active"':'' !!}>
                    <a data-toggle="tab" href="#passwordTab">
                        @trans(Безопасность)
                    </a>
                </li>
                <li {!! $activeTab == 'profileSetting'?'class="active"':'' !!}>
                    <a data-toggle="tab" href="#profileSetting">
                        @trans(Уведомления)
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="profile" class="profile-edit tab-pane {!! $activeTab == 'profile'?'active':'fade' !!}">
                    <h2 class="heading-md">
                        @trans(Личные данные)
                    </h2>
                    <br>
                    <form class="sky-form" id="sky-form4" action="{{url('user/save-profile')}}" method="post"
                          enctype="multipart/form-data">
                        <dl class="dl-horizontal">
                            <dt>@trans(Имя)</dt>
                            <dd>
                                <section>
                                    <label class="input">
                                        <i class="icon-append fa fa-user"></i>
                                        <input type="text" placeholder="Имя" name="profile[name]"
                                               value="<?= $user->profile->{UserProfile::NAME} ?>"/>
                                        <b class="tooltip tooltip-bottom-right">
                                            @trans(Будет отображаться на сайте)
                                        </b>
                                    </label>
                                </section>
                            </dd>
                            <dt>@trans(Фамилия)</dt>
                            <dd>
                                <section>
                                    <label class="input">
                                        <i class="icon-append fa fa-user"></i>
                                        <input type="text" placeholder="@trans(Фамилия)" name="profile[surname]"
                                               value="<?= $user->profile->{UserProfile::SURNAME} ?>">
                                        <b class="tooltip tooltip-bottom-right">
                                            @trans(Будет отображаться на сайте)
                                        </b>
                                    </label>
                                </section>
                            </dd>
                            <dt>@trans(Email)</dt>
                            <dd>
                                <section>
                                    <label class="input">
                                        <i class="icon-append fa fa-envelope"></i>
                                        <input type="email" placeholder="@trans(Email)" name="email" readonly
                                               value="<?=old('email', $user->email)?>">
                                        <b class="tooltip tooltip-bottom-right">
                                            @trans(Будет скрыто на сайте)
                                        </b>
                                    </label>
                                    @if ($errors->has('email'))
                                        <div class="alert alert-danger fade in">
                                            {{ $errors->first('email') }}
                                        </div>
                                    @endif
                                </section>
                            </dd>
                            <dt>@trans(Псевдоним)</dt>
                            <dd>
                                <section>
                                    <label class="input {!! ($errors->has('login')) ? 'state-error':'' !!}">
                                        <i class="icon-append fa fa-user"></i>
                                        <input type="text" placeholder="@trans(Псевдоним)" name="login"
                                               value="<?=old('login', $user->login)?>">
                                        <b class="tooltip tooltip-bottom-right">
                                            @trans(Псевдоним будет вести на Вашу персональную страницу)
                                        </b>
                                    </label>
                                    @if($errors->has('login'))
                                        <em for="fname" class="invalid">
                                            @foreach ($errors->get('login' ) as $message)
                                                {{$message}} <br>
                                            @endforeach
                                        </em>
                                    @endif
                                </section>
                            </dd>
                            <dt>@trans(Фото)</dt>
                            <dd>
                                <section>
                                    <label for="file" class="input input-file">
                                        <div class="button">
                                            <input type="file" name="photo"
                                                   onchange="this.parentNode.nextSibling.value = this.value">
                                            @trans(Выбрать)
                                        </div>
                                        <input type="text" readonly="">
                                        <label class="checkbox">
                                            <input type="checkbox" name="delete_photo"><i></i>
                                            @trans(Удалить фото)
                                        </label>
                                    </label>
                                </section>
                            </dd>
                            <dt>@trans(Телефон)</dt>
                            <dd>
                                <section>
                                    <label class="input">
                                        <i class="icon-append fa fa-phone"></i>
                                        <input type="text" placeholder="@trans(Телефон)" name="profile[phone]"
                                               value="<?=$user->profile->{UserProfile::PHONE}?>">
                                        <b class="tooltip tooltip-bottom-right">
                                            @trans(Будет скрыто на сайте)
                                        </b>
                                    </label>
                                </section>
                            </dd>
                            <dt>@trans(Пару слов о себе)</dt>
                            <dd>
                                <section>
                                    <label class="textarea">
                                        <textarea name="profile[about]" cols="30"
                                                  rows="10"><?=old('about', (isset($user->profile->data->about)) ?
                                                $user->profile->data->about : '')?></textarea>
                                    </label>
                                </section>
                            </dd>
                        </dl>
                        <button class="btn-u" type="submit">@trans(Сохранить)</button>
                    </form>
                </div>
                <div id="passwordTab"
                     class="profile-edit tab-pane  {!! $activeTab == 'passwordTab'?'active':'fade' !!}">
                    <h2 class="heading-md">@trans(Изменить настройки безопасности)</h2>
                    <br>
                    <form class="sky-form" id="sky-form4" action="{{url('user/save-password')}}" method="post">
                        <dl class="dl-horizontal">
                            <dt>@trans(Текущий пароль)</dt>
                            <dd>
                                <section>
                                    <label class="input {!! ($errors->has('current_password')) ? 'state-error':'' !!}">
                                        <i class="icon-append fa fa-lock"></i>
                                        <input type="password" name="current_password"
                                               autocomplete="new-password"
                                               placeholder="@trans(Текущий пароль)">
                                    </label>
                                    @if($errors->has('current_password'))
                                        <em for="fname" class="invalid">
                                            @foreach ($errors->get('current_password' ) as $message)
                                                {{$message}} <br>
                                            @endforeach
                                        </em>
                                    @endif
                                </section>
                            </dd>
                            <dt>@trans(Новый пароль)</dt>
                            <dd>
                                <section>
                                    <label class="input {!! ($errors->has('new_password')) ? 'state-error':'' !!}">
                                        <i class="icon-append fa fa-lock"></i>
                                        <input type="password" name="new_password"
                                               {!! ($errors->has('new_password')) ? 'class="invalid"':'' !!}
                                               autocomplete="new-password"
                                               placeholder="@trans(Новый пароль)">
                                    </label>
                                    @if($errors->has('new_password'))
                                        <em for="fname" class="invalid">
                                            @foreach ($errors->get('new_password' ) as $message)
                                                {{$message}}
                                            @endforeach
                                        </em>
                                    @endif
                                </section>
                            </dd>
                            <dt>@trans(Подтверждение пароля)</dt>
                            <dd>
                                <section>
                                    <label class="input">
                                        <i class="icon-append fa fa-lock"></i>
                                        <input type="password" name="new_password_confirmation"
                                               autocomplete="new-password"
                                               placeholder="@trans(Подтверждение пароля)">
                                    </label>
                                </section>
                            </dd>
                        </dl>
                        <button class="btn-u" type="submit">@trans(Сохранить)</button>
                    </form>
                </div>
                <div id="profileSetting"
                     class="profile-edit tab-pane  {!! $activeTab == 'profileSetting'?'active':'fade' !!}">
                    <form class="sky-form" id="sky-form3" action="{{url('cabinet/setting')}}" method="post">
                        <label class="toggle">
                            <input type="checkbox"
                                   {!! ($user->setting->can(\App\Modules\Users\Entity\UserSettings::SEND_EMAIL_NOTIFY))?'checked':'' !!}
                                   name="setting[{!! \App\Modules\Users\Entity\UserSettings::SEND_EMAIL_NOTIFY !!}]">
                            <i class="no-rounded"></i>
                            @trans(Получать уведомления по почте)
                        </label>
                        <hr>
                        <label class="toggle">
                            <input type="checkbox"
                                   {!! ($user->setting->can(\App\Modules\Users\Entity\UserSettings::SEND_PERSONAL_MESSAGES))?'checked':'' !!}
                                   name="setting[{!! \App\Modules\Users\Entity\UserSettings::SEND_PERSONAL_MESSAGES !!}]">
                            <i class="no-rounded"></i>
                            @trans(Получать уведомления на сайте)
                        </label>
                        <hr>
                        <label class="toggle">
                            <input type="checkbox"
                                   {!! ($user->setting->can(\App\Modules\Users\Entity\UserSettings::SHOW_PROFILE))?'checked':'' !!}
                                   name="setting[{!! \App\Modules\Users\Entity\UserSettings::SHOW_PROFILE !!}]">
                            <i class="no-rounded"></i>
                            @trans(Скрыть аккаунт)
                        </label>
                        <hr>
                        <button class="btn-u" type="submit">@trans(Сохранить)</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
