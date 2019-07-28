<?php

use FastDog\Config\Models\Emails;
use FastDog\Core\Properties\BaseProperties;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use FastDog\Core\Properties\BasePropertiesStorage;

class AppendConfigEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('config_emails')) {
            /** @var Emails $registration */
            $registration = Emails::create([
                Emails::NAME => trans('user::emails.Регистрация пользователя'),
                Emails::ALIAS => 'user_registration',
                Emails::TEXT => trans('user::emails.text_registration'),
                Emails::SITE_ID => '001',
                Emails::STATE => Emails::STATE_PUBLISHED,
                Emails::DATA => json_encode([]),
            ]);
            /** @var \Illuminate\Support\Collection $properties */
            $properties = $registration->properties();
            $saveProperties = [];
            $fillProperties = [
                'FROM_ADDRESS' => config('mail.from.address'),
                'SUBJECT' => trans('user::emails.Регистрация пользователя'),
                'TITLE' => trans('user::emails.Регистрация пользователя')];
            $properties->each(function ($property) use (&$saveProperties, $fillProperties) {
                if (isset($fillProperties[$property[BaseProperties::ALIAS]])) {
                    $property[BaseProperties::VALUE] = $fillProperties[$property[BaseProperties::ALIAS]];
                    $property['show'] = true;// <- активное свойство
                    $saveProperties[] = $property;
                }
            });

            $registration->storeProperties(collect($saveProperties));


            /** @var Emails $registrationConfirm */
            $registrationConfirm = Emails::create([
                Emails::NAME => trans('user::emails.Регистрация пользователя с подтверждением'),
                Emails::ALIAS => 'user_registration_confirm',
                Emails::TEXT => trans('user::emails.text_registration_confirm'),
                Emails::SITE_ID => '001',
                Emails::STATE => Emails::STATE_PUBLISHED,
                Emails::DATA => json_encode([]),
            ]);

            $fillProperties['SUBJECT'] = $fillProperties['TITLE'] = trans('user::emails.Регистрация пользователя с подтверждением');
            $properties->each(function ($property) use (&$saveProperties, $fillProperties) {
                if (isset($fillProperties[$property[BaseProperties::ALIAS]])) {
                    $property[BaseProperties::VALUE] = $fillProperties[$property[BaseProperties::ALIAS]];
                    $property['show'] = true;// <- активное свойство
                    $saveProperties[] = $property;
                }
            });

            $registrationConfirm->storeProperties(collect($saveProperties));

            /** @var Emails $resetPassword */
            $resetPassword = Emails::create([
                Emails::NAME => trans('user::emails.Сброс пароля'),
                Emails::ALIAS => 'new_password',
                Emails::TEXT => trans('user::emails.text_password_reset'),
                Emails::SITE_ID => '001',
                Emails::STATE => Emails::STATE_PUBLISHED,
                Emails::DATA => json_encode([]),
            ]);

            $fillProperties['SUBJECT'] = $fillProperties['TITLE'] = trans('user::emails.Сброс пароля');
            $properties->each(function ($property) use (&$saveProperties, $fillProperties) {
                if (isset($fillProperties[$property[BaseProperties::ALIAS]])) {
                    $property[BaseProperties::VALUE] = $fillProperties[$property[BaseProperties::ALIAS]];
                    $property['show'] = true;// <- активное свойство
                    $saveProperties[] = $property;
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Emails::whereIn(Emails::ALIAS, ['user_registration', 'user_registration_confirm', 'new_password'])
            ->get()->each(function (Emails $item) {
                BasePropertiesStorage::where([
                    BasePropertiesStorage::MODEL_ID => $item->getModelId(),
                    BasePropertiesStorage::ITEM_ID => $item->id,
                ])->delete();
                $item->forceDelete();
            });
    }
}
