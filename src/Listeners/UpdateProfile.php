<?php

namespace FastDog\User\Listeners;


use FastDog\User\Events\UserUpdate;
use FastDog\User\Models\Profile\UserProfile;
use FastDog\User\Models\Profile\UserProfileCorporate;
use FastDog\User\User;
use Illuminate\Http\Request;

/**
 * Обновление профиля
 *
 * Событие вызывается перед обновлением профиля, проверяет и корректирует набор переданных в модель данных
 *
 * @package FastDog\User\Listeners
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UpdateProfile
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * UpdateProfile constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Обработчик
     *
     * @param UserUpdate $event
     * @return void
     */
    public function handle(UserUpdate $event)
    {
        $user = $event->getUser();
        switch ($user->type) {
            case User::USER_TYPE_USER:
            case User::USER_TYPE_ADMIN:
                $item = UserProfile::where(UserProfile::USER_ID, $user->id)->first();
                if (!$item) {
                    $item = UserProfile::create([
                        UserProfile::USER_ID => $user->id,
                    ]);
                }
                $data = $this->request->input('profile');

                if ($this->request->has('profile_' . UserProfile::NAME)) {
                    $data[UserProfile::NAME] = $this->request->input('profile_' . UserProfile::NAME);
                }
                if ($this->request->has('profile_' . UserProfile::SURNAME)) {
                    $data[UserProfile::SURNAME] = $this->request->input('profile_' . UserProfile::SURNAME);
                }
                if ($this->request->has('profile_' . UserProfile::PATRONYMIC)) {
                    $data[UserProfile::PATRONYMIC] = $this->request->input('profile_' . UserProfile::PATRONYMIC);
                }

                if ($this->request->has(UserProfile::CITY_ID)) {
                    $data[UserProfile::CITY_ID] = $this->request->input(UserProfile::CITY_ID);
                }

                $data['data'] = (isset($data['data'])) ? json_encode($data['data']) : json_encode([]);

                UserProfile::where('id', $item->id)->update($data);
                break;
            case User::USER_TYPE_CORPORATE:
                $data = [
                    'legal_entity' => $this->request->input('profile.legal_entity'),
                    'title' => $this->request->input('profile.title'),
                    'country_id' => $this->request->input('profile.country_id'),
                    'inn' => $this->request->input('profile.inn'),
                    'cpp' => $this->request->input('profile.cpp'),
                    'okpo' => $this->request->input('profile.okpo'),
                    'index' => $this->request->input('profile.index'),
                    'region' => $this->request->input('profile.region'),
                    'area' => $this->request->input('profile.area'),
                    'city' => $this->request->input('profile->city'),
                    'settlement' => $this->request->input('profile.settlement'),
                    'street' => $this->request->input('profile.street'),
                    'house' => $this->request->input('profile.house'),
                    'structure' => $this->request->input('profile.structure'),
                    'office' => $this->request->input('profile.office'),

                    'same_addr' => $this->request->input('profile.same_addr'),
                    'f_country_id' => $this->request->input('profile.f_country_id'),
                    'f_index' => $this->request->input('profile.f_index'),
                    'f_region' => $this->request->input('profile.f_region'),
                    'f_area' => $this->request->input('profile.f_area'),
                    'f_city' => $this->request->input('profile.f_city'),
                    'f_settlement' => $this->request->input('profile.f_settlement'),
                    'f_house' => $this->request->input('profile.f_house'),
                    'f_structure' => $this->request->input('profile.f_structure'),
                    'f_office' => $this->request->input('profile.f_office'),

                    'general_manager' => $this->request->input('profile.general_manager'),
                    'chief_accountant' => $this->request->input('profile.chief_accountant'),
                    'phone_company' => $this->request->input('profile.phone_company'),
                    'email_organization' => $this->request->input('profile.email_organization'),
                    'contact_person' => $this->request->input('profile.contact_person'),
                    'telephone_contact_person' => $this->request->input('profile.telephone_contact_person'),
                    'email_contact_person' => $this->request->input('profile.email_contact_person'),
                    'current_account' => $this->request->input('profile.current_account'),
                    'bic' => $this->request->input('profile.bic'),
                    'bank' => $this->request->input('profile.bank'),
                    'correspondent_bank_account' => $this->request->input('profile.correspondent_bank_account'),
                    'fax' => $this->request->input('profile.fax'),
                    'site' => $this->request->input('profile.site'),

                    //'data' => json_encode($this->request->input('profile.data')),
                ];
                UserProfileCorporate::where('id', $this->request->input('profile.id'))->update($data);

                break;
        }
    }
}
