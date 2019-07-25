<?php

use FastDog\User\Models\UserConfig;
use Illuminate\Support\Facades\DB;
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
            Schema::create('users_config', function(Blueprint $table) {
                $table->increments('id');
                $table->string(UserConfig::NAME)->comment('Название');
                $table->string(UserConfig::ALIAS)->comment('Псевдоним');
                $table->json(UserConfig::VALUE)->nullable()->comment('Значение');
                $table->tinyInteger('priority')->default(100);
                $table->timestamps();
                $table->softDeletes();
                $table->unique(UserConfig::ALIAS, 'UK_users_config_alias');

            });
            DB::statement("ALTER TABLE `users_config` comment 'Параметры модуля Пользователи'");

            UserConfig::create([
                UserConfig::NAME => trans('user:config.public.name'),
                UserConfig::ALIAS => 'public',
                UserConfig::VALUE => json_encode([
                    [
                        'name' => trans('user:config.public.registration.name'),
                        'alias' => 'allow_registration',
                        'description' => trans('user:config.public.registration.description'),
                        'type' => 'type',
                        'value' => 'N'
                    ],
                    [
                        'name' => trans('user:config.public.registration_confirm.name'),
                        'alias' => 'allow_registration',
                        'description' => trans('user:config.public.registration_confirm.description'),
                        'type' => 'type',
                        'value' => 'Y'
                    ]
                ])
            ]);

            UserConfig::create([
                UserConfig::NAME => trans('user:config.desktop.name'),
                UserConfig::ALIAS => 'desktop',
                UserConfig::VALUE => json_encode([
                    [
                        [
                            'name' => trans('user:config.desktop.table.name'),
                            'alias' => 'allow_registration',
                            'description' => trans('user:config.desktop.table.description'),
                            'type' => 'graph',
                            'sort' => 100,
                            'value' => 'Y',
                            'data' => 'FastDog\\User\\Models\\Desktop\\VisitGraph::getData'
                        ],
                    ]
                ])
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
