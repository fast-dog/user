<?php

use FastDog\User\Models\UserConfig;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users_config')) {
            Schema::create('users_config', function (Blueprint $table) {
                $table->increments('id');
                $table->string(UserConfig::NAME)->comment('Название');
                $table->string(UserConfig::ALIAS)->comment('Псевдоним');
                $table->json(UserConfig::VALUE)->comment('Значение');
                $table->tinyInteger('priority');
                $table->timestamps();
                $table->softDeletes();
                $table->unique(UserConfig::ALIAS, 'UK_users_config_alias');

            });
            DB::statement("ALTER TABLE `users_config` comment 'Параметры модуля Пользователи'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_config');
    }
}
