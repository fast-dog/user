<?php

namespace FastDog\User\Listeners;

use FastDog\Core\Models\FormFieldTypes;
use FastDog\User\Events\UserMailingTemplatesAdminPrepare as EventUserMailingTemplatesAdminPrepare;
use FastDog\User\Models\UserMailingTemplates;
use Illuminate\Http\Request;

/**
 * Форма редактирования
 *
 * @package App\Modules\Content\Listeners
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserMailingTemplatesAdminSetEditorForm
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
     * @param EventUserMailingTemplatesAdminPrepare $event
     * @return void
     */
    public function handle(EventUserMailingTemplatesAdminPrepare $event)
    {
        $data = $event->getData();
        /** @var UserMailingTemplates $item */
        $item = $event->getItem();

        $result = $event->getResult();


        $result['form'] = [
            'create_url' => 'users/mailing/template/save',
            'update_url' => 'users/mailing/template/save',
            'help' => 'user_mailing_templates',
            'tabs' => (array)[
                (object)[
                    'id' => 'catalog-item-general-tab',
                    'name' => trans('user::forms.general.title'),
                    'active' => true,
                    'fields' => (array)[
                        [
                            'id' => UserMailingTemplates::NAME,
                            'type' => FormFieldTypes::TYPE_TEXT,
                            'name' => UserMailingTemplates::NAME,
                            'label' => trans('user::forms.templates.name'),
                            'css_class' => 'col-sm-12',
                            'form_group' => true,
                            'required' => true,
                            'validate' => 'required|min:5',
                        ],
                        [
                            'id' => UserMailingTemplates::TEXT,
                            'label' => trans('user::forms.templates.html'),
                            'type' => FormFieldTypes::TYPE_CODE_EDITOR,
                            'name' => UserMailingTemplates::TEXT,
                            'css_class' => 'col-sm-12',
                            'form_group' => true,
                            'required' => true,
                        ],
                    ],
                    'side' => [
                        [
                            'id' => UserMailingTemplates::CREATED_AT,
                            'type' => FormFieldTypes::TYPE_DATE,
                            'name' => UserMailingTemplates::CREATED_AT,
                            'label' => trans('user::forms.templates.date_created'),
                            'css_class' => 'col-sm-12',
                            'form_group' => true,
                            'readonly' => true,
                        ],

                    ],
                ],
                (object)[
                    'id' => 'menu-item-extend-tab',
                    'name' => trans('user::forms.templates.extend'),
                    'fields' => [
                        [
                            'type' => FormFieldTypes::TYPE_COMPONENT_SAMPLE_PROPERTIES,
                            'model_id' => $item->getModelId(),
                            'model' => UserMailingTemplates::class,
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