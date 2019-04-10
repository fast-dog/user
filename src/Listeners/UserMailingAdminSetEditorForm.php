<?php

namespace FastDog\User\Listeners;

use App\Core\FormFieldTypes;
use App\Modules\Config\Entity\DomainManager;
use FastDog\User\Entity\UserMailing;
use FastDog\User\Entity\UserMailingTemplates;
use FastDog\User\Events\UserMailingAdminPrepare as EventUserMailingAdminPrepare;
use Illuminate\Http\Request;

/**
 * Форма редактирования
 *
 * @package App\Modules\Content\Listeners
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserMailingAdminSetEditorForm
{
    /**
     * @var Request $request
     */
    protected $request;

    /**
     * UserMailingAdminSetEditorForm constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param EventUserMailingAdminPrepare $event
     * @return void
     */
    public function handle(EventUserMailingAdminPrepare $event)
    {
        $data = $event->getData();
        $item = $event->getItem();

        $result = $event->getResult();

        $result['form'] = [
            'create_url' => '',
            'update_url' => '',
            'help' => 'user_mailing',
            'tabs' => (array)[
                (object)[
                    'id' => 'catalog-item-general-tab',
                    'name' => trans('app.Основная информация'),
                    'active' => true,
                    'fields' => (array)[
                        [
                            'id' => UserMailing::NAME,
                            'type' => FormFieldTypes::TYPE_TEXT,
                            'name' => UserMailing::NAME,
                            'label' => trans('app.Название'),
                            'css_class' => 'col-sm-12',
                            'form_group' => true,
                            'required' => true,
                            'validate' => 'required|min:5',
                        ],
                        [
                            'id' => UserMailing::SUBJECT,
                            'type' => FormFieldTypes::TYPE_TEXT,
                            'name' => UserMailing::SUBJECT,
                            'label' => trans('app.Тема сообщения'),
                            'css_class' => 'col-sm-12',
                            'form_group' => true,
                            'required' => true,
                            'validate' => 'required|min:5',
                        ],
                        [
                            'id' => UserMailing::TEXT,
                            'label' => trans('app.HTML текст'),
                            'type' => FormFieldTypes::TYPE_HTML_EDITOR,
                            'name' => UserMailing::TEXT,
                            'form_group' => true,
                            'required' => true,
                        ],
                    ],
                    'side' => [
                        [
                            'id' => UserMailing::SITE_ID,
                            'type' => FormFieldTypes::TYPE_SELECT,
                            'name' => UserMailing::SITE_ID,
                            'label' => trans('app.Доступ'),
                            'items' => DomainManager::getAccessDomainList(),
                            'css_class' => 'col-sm-12',
                            'active' => DomainManager::checkIsDefault(),
                        ],
                        [
                            'id' => UserMailing::TEMPLATE_ID,
                            'type' => FormFieldTypes::TYPE_SELECT,
                            'name' => UserMailing::TEMPLATE_ID,
                            'label' => trans('app.Шаблон'),
                            'items' => UserMailingTemplates::getList(),
                            'css_class' => 'col-sm-12',
                        ],
                        [
                            'id' => UserMailing::CREATED_AT,
                            'type' => FormFieldTypes::TYPE_DATE,
                            'name' => UserMailing::CREATED_AT,
                            'label' => trans('app.Дата создания'),
                            'css_class' => 'col-sm-12',
                            'form_group' => true,
                        ],
                        [
                            'id' => UserMailing::START_AT,
                            'type' => FormFieldTypes::TYPE_DATE,
                            'name' => UserMailing::START_AT,
                            'label' => trans('app.Дата рассылки'),
                            'css_class' => 'col-sm-12',
                            'form_group' => true,
                        ],
                    ],
                ],
            ],
        ];


        if (config('app.debug')) {
            $data['_events'][] = __METHOD__;
        }

        $event->setData($data);
        $event->setResult($result);
    }
}