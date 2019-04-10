<?php

namespace FastDog\User\Models\Profile;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Профиль Юридического лица
 *
 * @package FastDog\User\Models\Profile
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserProfileCorporate extends AbstractProfile
{

    const USER_ID = 'user_id';
    const LEGAL_Models = 'legal_Models';
    const TITLE = 'title';
    const INN = 'inn';
    const CPP = 'cpp';
    const OKPO = 'okpo';
    const COUNTRY_ID = 'country_id';
    const INDEX = 'index';
    const REGION = 'region';
    const AREA = 'area';
    const CITY = 'city';
    const SETTLEMENT = 'settlement';
    const STREET = 'street';
    const HOUSE = 'house';
    const STRUCTURE = 'structure';
    const OFFICE = 'office';
    const F_COUNTRY_ID = 'f_country_id';
    const F_INDEX = 'f_index';
    const F_REGION = 'f_region';
    const F_AREA = 'f_area';
    const F_CITY = 'f_city';
    const F_SETTLEMENT = 'f_settlement';
    const F_STREET = 'f_street';
    const F_HOUSE = 'f_house';
    const F_STRUCTURE = 'f_structure';
    const F_OFFICE = 'f_office';
    const GENERAL_MANAGER = 'general_manager';
    const CHEF_ACCOUNTANT = 'chief_accountant';
    const PHONE_COMPANY = 'phone_company';
    const EMAIL_ORGANIZATION = 'email_organization';
    const CONTACT_PERSON = 'contact_person';
    const TELEPHONE_CONTACT_PERSON = 'telephone_contact_person';
    const EMAIL_CONTACT_PERSON = 'email_contact_person';
    const SHIPPING_ADDRESS = 'shipping_address';
    const CURRENT_ACCOUNT = 'current_account';
    const BIC = 'bic';
    const BANK = 'bank';
    const CORRESPONDENT_BANK_ACCOUNT = 'correspondent_bank_account';
    const SAME_ADDR = 'same_addr';
    const FAX = 'fax';
    const SITE = 'site';

    /**
     * Название таблицы
     *
     * @var string
     */
    public $table = 'users_profile_corporate';
    /**
     * Массив полей автозаполнения
     *
     * @var array $fillable
     */
    public $fillable = [
        self::USER_ID, self::LEGAL_Models, self::TITLE, self::INN, self::CPP, self::OKPO,
        self::COUNTRY_ID, self::INDEX, self::REGION, self::AREA, self::CITY, self::SETTLEMENT, self::STREET, self::HOUSE, self::STRUCTURE, self::OFFICE,
        self::F_COUNTRY_ID, self::F_INDEX, self::F_REGION, self::F_AREA, self::F_CITY, self::F_SETTLEMENT, self::F_STREET,
        self::F_HOUSE, self::F_STRUCTURE, self::F_OFFICE, self::GENERAL_MANAGER, self::CHEF_ACCOUNTANT, self::PHONE_COMPANY,
        self::EMAIL_ORGANIZATION, self::CONTACT_PERSON, self::TELEPHONE_CONTACT_PERSON, self::EMAIL_CONTACT_PERSON,
        self::SHIPPING_ADDRESS, self::CURRENT_ACCOUNT, self::BIC, self::BANK, self::CORRESPONDENT_BANK_ACCOUNT,
        self::SAME_ADDR, self::FAX, self::SITE,
    ];

    /**
     * Возможные формы юр.лица
     *
     * @return array
     */
    public function getLegalEntities()
    {
        return ['ООО', 'ИП', 'ЗАО', 'ОАО'];
    }
}
