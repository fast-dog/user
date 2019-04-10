<?php

use FastDog\User\Models\UserSettings;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users_settings')) {
            Schema::create('users_settings', function (Blueprint $table) {
                $table->increments('id');
                $table->integer(UserSettings::USER_ID);
                $table->tinyInteger(UserSettings::SEND_EMAIL_NOTIFY);
                $table->tinyInteger(UserSettings::SEND_PERSONAL_MESSAGES);
                $table->tinyInteger(UserSettings::SHOW_PROFILE);
                $table->timestamps();
            });
            DB::statement("ALTER TABLE `users_settings` comment 'Настройки пользователей'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_settings');
    }
}
