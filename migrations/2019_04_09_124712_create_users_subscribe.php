<?php

use FastDog\User\Models\UserEmailSubscribe;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersSubscribe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users_email_subscribe')) {
            Schema::create('users_email_subscribe', function (Blueprint $table) {
                $table->increments('id');
                $table->string(UserEmailSubscribe::EMAIL)->comment('Email');
                $table->tinyInteger(UserEmailSubscribe::STATE)
                    ->default(UserEmailSubscribe::STATE_PUBLISHED)->comment('Состояние');
                $table->char(UserEmailSubscribe::SITE_ID, 3)->default('001')->comment('Код сайта');
                $table->timestamps();
                $table->softDeletes();

            });
            DB::statement("ALTER TABLE `users_email_subscribe` comment 'Подписки пользователей на рассылку'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_email_subscribe');
    }
}
