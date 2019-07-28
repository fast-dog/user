<?php

namespace FastDog\User\Listeners;


use FastDog\Core\Models\DomainManager;
use FastDog\Core\Models\FormFieldTypes;
use FastDog\User\Events\UserMailingAdminPrepare as EventUserMailingAdminPrepare;
use FastDog\User\Models\UserMailing;
use FastDog\User\Models\UserMailingTemplates;
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
            'create_url' => 'users/mailing/save',
            'update_url' => 'users/mailing/update',
            'help' => 'user_mailing',
            'tabs' => (array)[
                (object)[
                    'id' => 'catalog-item-general-tab',
                    'name' => trans('user::forms.general.title'),
                    'active' => true,
                    'fields' => (array)[
                        [
                            'id' => UserMailing::NAME,
                            'type' => FormFieldTypes::TYPE_TEXT,
                            'name' => UserMailing::NAME,
                            'label' => trans('user::forms.mailing.name'),
                            'css_class' => 'col-sm-12',
                            'form_group' => true,
                            'required' => true,
                            'validate' => 'required|min:5',
                        ],
                        [
                            'id' => UserMailing::SUBJECT,
                            'type' => FormFieldTypes::TYPE_TEXT,
                            'name' => UserMailing::SUBJECT,
                            'label' => trans('user::forms.mailing.subject'),
                            'css_class' => 'col-sm-12',
                            'form_group' => true,
                            'required' => true,
                            'validate' => 'required|min:5',
                        ],
                        [
                            'id' => UserMailing::TEXT,
                            'label' => trans('user::forms.mailing.html'),
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
                            'label' => trans('user::forms.mailing.access'),
                            'items' => DomainManager::getAccessDomainList(),
                            'css_class' => 'col-sm-12',
                            'required' => true,
                            'active' => DomainManager::checkIsDefault(),
                        ],
                        [
                            'id' => UserMailing::TEMPLATE_ID,
                            'type' => FormFieldTypes::TYPE_SELECT,
                            'name' => UserMailing::TEMPLATE_ID,
                            'label' => trans('user::forms.mailing.template'),
                            'items' => UserMailingTemplates::getList(),
                            'css_class' => 'col-sm-12',
                        ],
                        [
                            'id' => UserMailing::CREATED_AT,
                            'type' => FormFieldTypes::TYPE_DATE,
                            'name' => UserMailing::CREATED_AT,
                            'label' => trans('user::forms.mailing.date_create'),
                            'css_class' => 'col-sm-12',
                            'form_group' => true,
                        ],
                        [
                            'id' => UserMailing::START_AT,
                            'type' => FormFieldTypes::TYPE_DATE,
                            'name' => UserMailing::START_AT,
                            'required' => true,
                            'label' => trans('user::forms.mailing.date_sending'),
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
