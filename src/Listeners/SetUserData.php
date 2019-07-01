<?php

namespace FastDog\User\Listeners;


use FastDog\Core\Models\ModuleManager;
use FastDog\Media\Entity\GalleryItem;
use FastDog\User\Events\GetUserData;
use FastDog\User\Models\Profile\UserProfile;
use FastDog\User\Models\Profile\UserProfileCorporate;
use FastDog\User\Models\UserEmails;
use FastDog\User\User;
use Illuminate\Http\Request;

/**
 * Обтаботка пользователских данных
 *
 * Событие добавляет массив данных в зависимости от типа учетной записи
 *
 * @package FastDog\User\Listeners
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class SetUserData
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * SetUserData constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Обрбаотчик события
     *
     * Событие добавляет массив данных в зависимости от типа учетной записи, если профиля несуществует - создает его
     *
     * @param GetUserData $event
     * @return void
     */
    public function handle(GetUserData $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        $data['created_at'] = ($user->created_at !== null) ? $user->created_at->format('Y-m-d') : '';
        $data['updated_at'] = ($user->updated_at !== null) ? $user->updated_at->format('Y-m-d') : '';
        $data['published_at'] = ($user->published_at !== null) ? $user->published_at->format('Y-m-d') : '';

        $phoneMask = '+9 (999) 999-99?-999';
        /**
         * @var $moduleManager ModuleManager
         */
        $moduleManager = \App::make(ModuleManager::class);

        $data['order_module'] = ($moduleManager->hasModule('App\Modules\Order\Order')) ? 'Y' : 'N';

        if (($user->id > 0) && (isset($user->profile->data) && ($user->profile->data !== null))) {
            $user->profile->data = json_decode($user->profile->data);
           // if (!isset($user->profile->data->phone_mask)) {
               // $user->profile->data->phone_mask = $phoneMask;
            //}
            $_data = $user->profile->data;
        } else {
            $_data = (object)[
                'phone_mask' => $phoneMask,
            ];
        }

        switch ($user->type) {
            case User::USER_TYPE_USER:
            case User::USER_TYPE_ADMIN:
                if ($user->id > 0) {
                    if ($user->profile == null) {
                        $user->profile = UserProfile::create([
                            UserProfile::USER_ID => $user->id,
                        ]);
                    }
                }
                if (!isset($_data->location)) {
                    $_data->location = '';
                }
                if (!isset($_data->soc)) {
                    $_data->soc = (object)[
                        'g' => '',
                        'f' => ''
                    ];
                }
                if (!isset($_data->children)) {
                    $_data->children = [];
                }
                $data['profile'] = [
                    'name' => isset($user->profile->name) ? $user->profile->name : '',
                    'surname' => isset($user->profile->surname) ? $user->profile->surname : '',
                    'patronymic' => isset($user->profile->patronymic) ? $user->profile->patronymic : '',
                    'phone' => isset($user->profile->phone) ? $user->profile->phone : '',
                    'delivery' => (isset($user->profile->delivery)) ? $user->profile->delivery : '',
                    'birth' => (isset($user->profile->birth) && $user->profile->birth != '0000-00-00') ?
                        $user->profile->birth : '',
                    'data' => $_data,
                ];
                break;
            case User::USER_TYPE_CORPORATE:
                if ($user->profile == null) {
                    $user->profile = UserProfileCorporate::create([
                        UserProfile::USER_ID => $user->id,
                    ]);
                }
                $data['profile'] = [
                    'id' => $user->profile->id,
                    'legal_entity' => $user->profile->legal_entity,
                    'title' => $user->profile->title,
                    'country_id' => $user->profile->country_id,
                    'inn' => $user->profile->inn,
                    'cpp' => $user->profile->cpp,
                    'okpo' => $user->profile->okpo,
                    'index' => $user->profile->index,
                    'region' => $user->profile->region,
                    'area' => $user->profile->area,
                    'city' => $user->profile->city,
                    'settlement' => $user->profile->settlement,
                    'street' => $user->profile->street,
                    'house' => $user->profile->house,
                    'structure' => $user->profile->structure,
                    'office' => $user->profile->office,

                    'same_addr' => $user->profile->same_addr,
                    'f_country_id' => $user->profile->f_country_id,
                    'f_index' => $user->profile->f_index,
                    'f_region' => $user->profile->f_region,
                    'f_area' => $user->profile->f_area,
                    'f_city' => $user->profile->f_city,
                    'f_settlement' => $user->profile->f_settlement,
                    'f_house' => $user->profile->f_house,
                    'f_structure' => $user->profile->f_structure,
                    'f_office' => $user->profile->f_office,

                    'general_manager' => $user->profile->general_manager,
                    'chief_accountant' => $user->profile->chief_accountant,
                    'phone_company' => $user->profile->phone_company,
                    'email_organization' => $user->profile->email_organization,
                    'contact_person' => $user->profile->contact_person,
                    'telephote_contact_person' => $user->profile->telephote_contact_person,
                    'email_contact_person' => $user->profile->email_contact_person,
                    'current_account' => $user->profile->current_account,
                    'bic' => $user->profile->bic,
                    'bank' => $user->profile->bank,
                    'correspondent_bank_account' => $user->profile->correspondent_bank_account,
                    'fax' => $user->profile->fax,
                    'site' => $user->profile->site,

                    'data' => $user->profile->data,
                ];
                break;
        }

        $data['messages'] = UserEmails::getEmailMessageList($this->request, $user);

        /**
         * Параметры загрузчика
         */
        $data['el_finder'] = [
            GalleryItem::PARENT_TYPE => GalleryItem::TYPE_USER_PHOTO,
            GalleryItem::PARENT_ID => (isset($user->id)) ? $user->id : 0,
        ];

        $data['files_module'] = ($moduleManager->hasModule('App\Modules\Media\Media')) ? 'Y' : 'N';

        if (config('app.debug')) {
            $data['_events'][] = __METHOD__;
        }

        $event->setData($data);
    }
}
