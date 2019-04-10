<?php

use FastDog\User\Models\Profile\UserProfile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersProfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users_profile')) {
            Schema::create('users_profile', function (Blueprint $table) {
                $table->increments('id');
                $table->integer(UserProfile::USER_ID)->default(0)->comment('Идентификатор пользователя');
                $table->string(UserProfile::NAME, 50)->comment('Имя');
                $table->string(UserProfile::SURNAME, 50)->comment('Фамилия');
                $table->string(UserProfile::PATRONYMIC, 50)->comment('Отчество');
                $table->string(UserProfile::PHONE, 50)->comment('Контактный телефон');
                $table->json('data')->comment('Дополнительные параметры');
                $table->timestamps();
                $table->softDeletes();
                $table->unique(UserProfile::USER_ID, 'UK_users_profile_user_id');
            });
            DB::statement("ALTER TABLE `users_profile` comment 'Профили пользователей'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_profile');
    }
}
