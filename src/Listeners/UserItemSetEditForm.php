<?php

namespace FastDog\User\Listeners;

use FastDog\Core\Models\DomainManager;
use FastDog\Core\Models\FormFieldTypes;
use FastDog\Core\Models\ModuleManager;
use FastDog\User\Events\UserAdminPrepare as UserAdminPrepareEvent;
use FastDog\User\Models\Profile\UserProfile;
use FastDog\User\Models\Profile\UserProfileCorporate;
use FastDog\User\User;
use Illuminate\Http\Request;

/**
 * Обработка данных в разделе администрирования
 *
 * Событие добавляет поля инициализации формы редактирования
 *
 * @package App\Modules\Menu\Listeners
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserItemSetEditForm
{

    /**
     * @var Request $request
     */
    protected $request;

    /**
     * AfterSave constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param UserAdminPrepareEvent $event
     * @return void
     */
    public function handle(UserAdminPrepareEvent $event)
    {
        /**
         * @var $moduleManager ModuleManager
         */
        $moduleManager = \App::make(ModuleManager::class);
        /**
         * @var $item User
         */
        $item = $event->getItem();

        /**
         * @var $result array
         */
        $result = $event->getResult();


        $result['form'] = [
            'create_url' => 'user/add',
            'update_url' => 'user/add',
            'tabs' => (array)[
                (object)[
                    'id' => 'user-general-tab',
                    'name' => trans('user::forms.general.title'),
                    'active' => true,
                    'fields' => (array)[
                        [
                            'id' => User::TYPE,
                            'type' => FormFieldTypes::TYPE_SELECT,
                            'name' => User::TYPE,
                            'label' => trans('user::forms.general.fields.type'),
                            'css_class' => 'col-sm-6',
                            'form_group' => false,
                            'items' => User::getAllType(),
                        ],
                        [
                            'type' => FormFieldTypes::TYPE_TEXT,
                            'name' => User::EMAIL,
                            'label' => trans('user::forms.general.fields.email'),
                            'css_class' => 'col-sm-6',
                            'form_group' => false,
                        ],
                        [
                            'id' => 'password',
                            'type' => FormFieldTypes::TYPE_PASSWORD,
                            'name' => User::PASSWORD,
                            'label' => trans('user::forms.general.fields.password'),
                            'css_class' => 'col-sm-6',
                            'form_group' => false,
                        ],
                    ],
                    'side' => [
                        [
                            'id' => User::STATUS,
                            'type' => FormFieldTypes::TYPE_SELECT,
                            'name' => User::STATUS,
                            'label' => trans('user::forms.general.fields.state'),
                            'css_class' => 'col-sm-12',
                            'items' => User::getStatusList(),
                        ],
                        [
                            'id' => 'access',
                            'type' => FormFieldTypes::TYPE_ACCESS_LIST,
                            'name' => User::SITE_ID,
                            'label' => trans('user::forms.general.fields.access'),
                            'items' => DomainManager::getAccessDomainList(),
                            'active' => DomainManager::checkIsDefault(),
                            'css_class' => 'col-sm-12',
                        ],
//                        [
//                            'id' => User::GROUP_ID,
//                            'type' => FormFieldTypes::TYPE_SELECT,
//                            'name' => User::GROUP_ID,
//                            'label' => trans('app.Роль ACL'),
//                            'css_class' => 'col-sm-12',
//                            'items' => $item->getAclRoles(),
//                        ],
                        [
                            'id' => User::CREATED_AT,
                            'type' => FormFieldTypes::TYPE_DATE,
                            'name' => User::CREATED_AT,
                            'label' => trans('user::forms.general.fields.created_at'),
                            'css_class' => 'col-sm-12',
                            'form_group' => true,
                        ],
                        [
                            'id' => User::UPDATED_AT,
                            'type' => FormFieldTypes::TYPE_DATE,
                            'name' => User::UPDATED_AT,
                            'label' => trans('user::forms.general.fields.updated_at'),
                            'css_class' => 'col-sm-12',
                            'form_group' => true,
                            'readonly' => true,
                        ],
                    ],
                ],
                (object)[
                    'id' => 'user-profile-tab',
                    'name' => trans('user::forms.profile.title'),
                    'active' => false,
                    'fields' => [
//                        [
//                            'id' => UserProfileCorporate::LEGAL_ENTITY,
//                            'type' => FormFieldTypes::TYPE_SELECT,
//                            'name' => UserProfileCorporate::LEGAL_ENTITY,
//                            'label' => trans('app.Форма юридического лица'),
//                            'css_class' => 'col-sm-6', 'form_group' => false,
//                            'items' => User::getAllTypeCorporate(),//
//                            'expression' => 'function(item){ return (item.type == "corporate"); }',
//                        ],
//                        [
//                            'id' => UserProfileCorporate::TITLE,
//                            'type' => FormFieldTypes::TYPE_TEXT,
//                            'name' => UserProfileCorporate::LEGAL_ENTITY,
//                            'label' => trans('app.Наименование организации'),
//                            'css_class' => 'col-sm-6',
//                            'form_group' => false,
//                            'expression' => 'function(item){ return (item.type == "corporate"); }',
//                        ],
                        [
                            'id' => UserProfileCorporate::INN,
                            'type' => FormFieldTypes::TYPE_TEXT,
                            'name' => UserProfileCorporate::INN,
                            'label' => trans('user::forms.profile.fields.inn'),
                            'css_class' => 'col-sm-6',
                            'form_group' => false,
                            'expression' => 'function(item){ return (item.type == "corporate"); }',
                        ],
                        [
                            'id' => UserProfileCorporate::CPP,
                            'type' => FormFieldTypes::TYPE_TEXT,
                            'name' => UserProfileCorporate::CPP,
                            'label' => trans('user::forms.profile.fields.cpp'),
                            'css_class' => 'col-sm-6',
                            'form_group' => false,
                            'expression' => 'function(item){ return (item.type == "corporate"); }',
                        ],
                        [
                            'id' => UserProfileCorporate::OKPO,
                            'type' => FormFieldTypes::TYPE_TEXT,
                            'name' => UserProfileCorporate::OKPO,
                            'label' => trans('user::forms.profile.fields.okpo'),
                            'css_class' => 'col-sm-6',
                            'form_group' => false,
                            'expression' => 'function(item){ return (item.type == "corporate"); }',
                        ],
                        //... <--  Все заполнять нет смысла, используется в 1 из 100 проектов и набор полей инивидуален,
                        //..       при необходимости использовать событие для изменения полей профиля
                        [
                            'id' => UserProfile::NAME,
                            'type' => FormFieldTypes::TYPE_TEXT,
                            'name' => UserProfile::NAME,
                            'label' => trans('user::forms.profile.fields.name'),
                            'css_class' => 'col-sm-6',
                            'form_group' => false,
                            'expression' => 'function(item){ return (item.type == "user" || item.type == "admin"); }',
                        ],
                        [
                            'id' => UserProfile::PATRONYMIC,
                            'type' => FormFieldTypes::TYPE_TEXT,
                            'name' => UserProfile::PATRONYMIC,
                            'label' => trans('user::forms.profile.fields.patronymic'),
                            'css_class' => 'col-sm-6',
                            'form_group' => false,
                            'expression' => 'function(item){ return (item.type == "user" || item.type == "admin"); }',
                        ],
                    ],
                ],
            ],
        ];

        $event->setResult($result);

        $data = $event->getData();
        if (config('app.debug')) {
            $data['_events'][] = __METHOD__;
        }
        $event->setData($data);

    }
}