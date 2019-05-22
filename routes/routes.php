<?php

use FastDog\Core\Models\DomainManager;

Route::group([
    'prefix' => config('core.admin_path', 'admin'),
    'middleware' => ['web'],
], function () {


    $baseParameters = ['middleware' => []];


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
    \Route::post('/user/add', array_replace_recursive($baseParameters, [
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

    //сохранение параметров модуля
    \Route::post('/users/save-module-configurations', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@postSaveModuleConfigurations',
    ]));

    //сохранение параметров модуля
    \Route::post('/users/access', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@postAccess',
    ]));
    //
    \Route::get('/user/admin-info', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@getAdminInfo',
    ]));

    // список активных
    \Route::get('/user/mailing/process', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@getMailingProcess',
    ]));

    /**
     * Подписки на рассылку
     */
    $ctrl = '\FastDog\User\Http\Controllers\Admin\UserSubscribeTableController';

    // таблица подписок
    \Route::post('/user/subscribe', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@list',
    ]));

    // обновление
    \Route::post('/user/subscribe/self-update', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@postItemSelfUpdate',
    ]));
    //
    \Route::get('/user/subscribe/csv', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@getSubscribeCsv',
    ]))->where(['id' => '[1-90]+']);

    /**
     * Рассылка - шаблоны - таблица
     */
    $ctrl = '\FastDog\User\Http\Controllers\Admin\UserMailingTemplatesTableController';
    //
    \Route::post('/user/mailing/templates', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@list',
    ]));

    /**
     * Рассылка - шаблоны - форма
     */
    $ctrl = '\FastDog\User\Http\Controllers\Admin\UserMailingTemplatesFormController';

    // редактирование рассылки
    \Route::get('/user/mailing/template/{id?}', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@getEditItem',
    ]))->where('id', '[1-90]+');

    // список рассылок
    \Route::post('/user/mailing/template/save', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@postMailingTemplate',
    ]));

    /**
     * Рассылка - таблица
     */
    $ctrl = '\FastDog\User\Http\Controllers\Admin\UserMailingTableController';
    // список рассылок
    \Route::post('/user/mailing', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@list',
    ]));

    /**
     * Рассылка - форма
     */
    $ctrl = '\FastDog\User\Http\Controllers\Admin\UserMailingFormController';
    // редактирование рассылки
    \Route::get('/user/mailing/{id?}', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@getEditItem',
    ]))->where('id', '[1-90]+');

    \Route::post('/user/mailing/save', array_replace_recursive($baseParameters, [
        'uses' => $ctrl . '@postMailing',
    ]));
});


$ctrl = '\FastDog\User\Http\Controllers\Site\UserController';

//\Route::post('/login', '\FastDog\User\Http\Controllers\Site\LoginController@postLogin');
//\Route::get('/logout', '\FastDog\User\Http\Controllers\Site\LoginController@logout');

\Route::post('/subscribe', $ctrl . '@postSubscribe');
\Route::get('/subscribe/off', $ctrl . '@getSubscribeOff');

/**
 * Не используется в проекте!!!
 *
 *
 * \Route::get('/confirm/{hash}', '\FastDog\User\Http\Controllers\Site\LoginController@confirm');
 * \Route::get('/confirm-password/{hash}', '\FastDog\User\Http\Controllers\Site\LoginController@confirmPassword');
 * \Route::post('/restore-password', '\FastDog\User\Http\Controllers\Site\LoginController@sendPassword');
 *
 * $ctrl = '\FastDog\User\Http\Controllers\Site\RegistrationController';
 * \Route::post('/registration', $ctrl . '@postRegistration');
 *
 *
 * $ctrl = '\FastDog\User\Http\Controllers\Site\SocialController';
 * \Route::get('auth/{driver}', ['as' => 'socialAuth', 'uses' => $ctrl . '@redirectToProvider']);
 * \Route::get('auth/{driver}/callback', ['as' => 'socialAuthCallback', 'uses' => $ctrl . '@handleProviderCallback']);
 *
 * $ctrl = '\FastDog\User\Http\Controllers\Site\CabinetController';
 * \Route::post('user/save-profile', ['as' => 'socialAuth', 'uses' => $ctrl . '@saveProfile']);
 * \Route::post('user/save-password', ['as' => 'socialAuth', 'uses' => $ctrl . '@postSavePassword']);
 *
 * \Route::post('cabinet/new-message', ['as' => 'socialAuth', 'uses' => $ctrl . '@postNewMessage']);
 *
 * //изменение фото профиля
 * \Route::post('cabinet/change-photo', $ctrl . '@postChangePhoto');
 * //проверка и создание города
 * \Route::post('cabinet/check-locality', $ctrl . '@postCheckLocality');
 * //обновление профиля
 * \Route::post('cabinet/update', $ctrl . '@saveProfile');
 * //удаление различных данных
 * \Route::post('cabinet/delete-favorites', $ctrl . '@deleteFavorites');
 * //сохранение настроек пользователя
 * \Route::post('cabinet/setting', $ctrl . '@postSetting');
 *
 * //публичный просмотр профиля
 * \Route::get('user/{id}', $ctrl . '@getPublicProfile')->where('id', '[1-90]+');
 * \Route::get('user/{login}', $ctrl . '@getPublicProfile');
 * //добавление пользователя в закладки
 * \Route::post('user/add-favorites', $ctrl . '@postAddFavorites');
 *
 * //постраничный вывод сообщений
 * \Route::post('cabinet/messages', $ctrl . '@postMessages');
 * //удаление всех сообщений чата
 * \Route::post('cabinet/clear-messages', $ctrl . '@postClearMessages');
 * //добавление файла к сообщению
 * \Route::post('cabinet/attach', $ctrl . '@postMessagesAttach');
 * //удаление загруженного файла
 * \Route::post('cabinet/delete-attach', $ctrl . '@postDeleteMessagesAttach');
 */