<?php

use FastDog\User\Models\Profile\UserProfileCorporate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersProfileCorporate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users_profile_corporate')) {
            Schema::create('users_profile_corporate', function (Blueprint $table) {
                $table->increments('id');
                $table->integer(UserProfileCorporate::USER_ID)->default(0)->comment('Идентификатор пользователя');
                $table->string(UserProfileCorporate::LEGAL_Models, 100)->nullable();
                $table->string(UserProfileCorporate::TITLE, 100)->nullable();
                $table->string(UserProfileCorporate::INN, 100)->nullable();
                $table->string(UserProfileCorporate::CPP, 100)->nullable();
                $table->string(UserProfileCorporate::OKPO, 100)->nullable();
                $table->integer(UserProfileCorporate::COUNTRY_ID)->default(0);
                $table->string(UserProfileCorporate::INDEX, 100)->nullable();
                $table->string(UserProfileCorporate::REGION, 100)->nullable();
                $table->string(UserProfileCorporate::AREA, 100)->nullable();
                $table->string(UserProfileCorporate::CITY, 100)->nullable();
                $table->string(UserProfileCorporate::SETTLEMENT, 100)->nullable();
                $table->string(UserProfileCorporate::STREET, 100)->nullable();
                $table->string(UserProfileCorporate::HOUSE, 100)->nullable();
                $table->string(UserProfileCorporate::STRUCTURE, 100)->nullable();
                $table->string(UserProfileCorporate::OFFICE, 100)->nullable();
                $table->integer(UserProfileCorporate::F_COUNTRY_ID)->nullable();
                $table->string(UserProfileCorporate::F_INDEX, 100)->nullable();
                $table->string(UserProfileCorporate::F_REGION, 100)->nullable();
                $table->string(UserProfileCorporate::F_AREA, 100)->nullable();
                $table->string(UserProfileCorporate::F_CITY, 100)->nullable();
                $table->string(UserProfileCorporate::F_SETTLEMENT, 100)->nullable();
                $table->string(UserProfileCorporate::F_STREET, 100)->nullable();
                $table->string(UserProfileCorporate::F_HOUSE, 100)->nullable();
                $table->string(UserProfileCorporate::F_STRUCTURE, 100)->nullable();
                $table->string(UserProfileCorporate::F_OFFICE, 100)->nullable();
                $table->string(UserProfileCorporate::GENERAL_MANAGER, 100)->nullable();
                $table->string(UserProfileCorporate::CHEF_ACCOUNTANT, 100)->nullable();
                $table->string(UserProfileCorporate::PHONE_COMPANY, 100)->nullable();
                $table->string(UserProfileCorporate::EMAIL_ORGANIZATION, 100)->nullable();
                $table->string(UserProfileCorporate::CONTACT_PERSON, 100)->nullable();
                $table->string(UserProfileCorporate::TELEPHONE_CONTACT_PERSON, 100)->nullable();
                $table->string(UserProfileCorporate::EMAIL_CONTACT_PERSON, 100)->nullable();
                $table->string(UserProfileCorporate::SHIPPING_ADDRESS, 100)->nullable();
                $table->string(UserProfileCorporate::CURRENT_ACCOUNT, 100)->nullable();
                $table->string(UserProfileCorporate::BIC, 100)->nullable();
                $table->string(UserProfileCorporate::BANK, 100)->nullable();
                $table->string(UserProfileCorporate::CORRESPONDENT_BANK_ACCOUNT, 100)->nullable();
                $table->string(UserProfileCorporate::SAME_ADDR, 100)->nullable();
                $table->string(UserProfileCorporate::FAX, 100)->nullable();
                $table->string(UserProfileCorporate::SITE, 100)->nullable();

                $table->json('data')->comment('Дополнительные параметры');
                $table->timestamps();
                $table->softDeletes();
                $table->unique(UserProfileCorporate::USER_ID, 'UK_users_profile_corporate_user_id');
            });
            DB::statement("ALTER TABLE `users_profile_corporate` comment 'Профили корпоративных клиентов'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_profile_corporate');
    }
}
