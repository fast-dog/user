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

            UserConfig::create([
                UserConfig::NAME => 'Настройки публичного раздела',
                UserConfig::ALIAS => 'public',
                UserConfig::VALUE => <<<JSON
[
  {
    "name": "Разрешить регистрацию",
    "alias": "allow_registration",
    "description": "Разрешить регистрацию на сайте",
    "type": "select",
    "value": "N"
  },
  {
    "name": "Требовать подтверждение email",
    "alias": "registration_confirm",
    "description": "Разрешить регистрацию на сайте",
    "type": "select",
    "value": "Y"
  }
]
JSON
                ,
            ]);

            UserConfig::create([
                UserConfig::NAME => 'Администрирование: Рабочий стол',
                UserConfig::ALIAS => 'desktop',
                UserConfig::VALUE => <<<JSON
[
  {
    "type": "graph",
    "name": "Пользователи :: статистика регистраций",
    "sort": "100",
    "description": "Отображает график статистики регистраций на главной странице раздела администрирования.",
    "value": "Y",
    "data": "FastDog\\User\\Models\\Desktop\\RegisterGraph::getData"
  },
  {
    "type": "table",
    "name": "Пользователи :: статистика посещений",
    "description": "Отображает график статистики посещений на главной странице раздела администрирования.",
    "sort": "100",
    "value": "Y",
    "data": "FastDog\\User\\Models\\Desktop\\VisitGraph::getData"
  }
]
JSON
                ,
            ]);
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
