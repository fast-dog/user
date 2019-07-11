<?php

use FastDog\Core\Models\DomainManager;

Route::group([
    'prefix' => config('core.admin_path', 'admin'),
    'middleware' => ['web'],
], function () {


    $baseParameters = ['middleware' => [
        \FastDog\Admin\Http\Middleware\Admin::class,
    ]];


    // список пользователей
    \Route::post('/users', array_replace_recursive($baseParameters, [
        'uses' => '\FastDog\User\Http\Controllers\Admin\UserTableController@list',
    ]));


    $ctrl = '\FastDog\User\Http\Controllers\Admin\UserFormController';

    // редактирование пользователя
    \Route::get('/user/{id?}', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@getEditItem',
    ]))->where(['id' => '[1-90]+']);


    //добавление пользователя
    \Route::post('/users/update', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@postUser',
    ]));

    //удаление пользователя
    \Route::post('/user/delete', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@postUserDelete',
    ]));
    /**
     * Пользователи - API
     */
    $ctrl = '\FastDog\User\Http\Controllers\Admin\ApiController';
//
//    //сохранение параметров модуля
//    \Route::post('/users/save-module-configurations', array_replace_recursive($baseParameters, [
//        'uses' => $ctrl . '@postSaveModuleConfigurations',
//    ]));
//
//    //сохранение параметров модуля
//    \Route::post('/users/access', array_replace_recursive($baseParameters, [
//        'uses' => $ctrl . '@postAccess',
//    ]));
//    //
//    \Route::get('/user/admin-info', array_replace_recursive($baseParameters, [
//        'uses' => $ctrl . '@getAdminInfo',
//    ]));
//
    // список активных
    \Route::get('/users/mailing/process', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@getMailingProcess',
    ]));

    /**
     * Подписки на рассылку
     */
    $ctrl = '\FastDog\User\Http\Controllers\Admin\UserSubscribeTableController';

    // таблица подписок
    \Route::post('/users/subscribe', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@list',
    ]));

    // обновление
    \Route::post('/users/subscribe/update', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@postItemSelfUpdate',
    ]));
    //
    \Route::get('/users/subscribe/csv', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@getSubscribeCsv',
    ]))->where(['id' => '[1-90]+']);

    /**
     * Рассылка - шаблоны - таблица
     */
    $ctrl = '\FastDog\User\Http\Controllers\Admin\UserMailingTemplatesTableController';
    //
    \Route::post('/users/mailing/templates', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@list',
    ]));

    /**
     * Рассылка - шаблоны - форма
     */
    $ctrl = '\FastDog\User\Http\Controllers\Admin\UserMailingTemplatesFormController';

    // редактирование шаблона рассылки
    \Route::get('/users/mailing/template/{id?}', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@getEditItem',
    ]))->where('id', '[1-90]+');

    // сохранение шаблона рассылки
    \Route::post('/users/mailing/template/save', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@postMailingTemplate',
    ]));

    // обновление/удаление шаблона рассылки
    \Route::post('/users/mailing/templates/update', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@postMailingTemplateUpdate',
    ]));

    /*
     * Рассылка - таблица
     */
    $ctrl = '\FastDog\User\Http\Controllers\Admin\UserMailingTableController';
    // список рассылок
    \Route::post('/users/mailing', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@list',
    ]));

    /**
     * Рассылка - форма
     */
    $ctrl = '\FastDog\User\Http\Controllers\Admin\UserMailingFormController';
    // редактирование рассылки
    \Route::get('/users/mailing/{id?}', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@getEditItem',
    ]))->where('id', '[1-90]+');

    \Route::post('/users/mailing/save', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@postMailing',
    ]));
});


$ctrl = '\FastDog\User\Http\Controllers\Site\UserController';

//\Route::post('/login', '\FastDog\User\Http\Controllers\Site\LoginController@postLogin');
//\Route::get('/logout', '\FastDog\User\Http\Controllers\Site\LoginController@logout');

\Route::post('/subscribe', $ctrl . '@postSubscribe');
\Route::get('/subscribe/off', $ctrl . '@getSubscribeOff');
