<?php

use FastDog\User\Models\UserEmails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users_emails')) {
            Schema::create('users_emails', function (Blueprint $table) {
                $table->increments('id');
                $table->string(UserEmails::SUBJECT)->comment('Тема письма');
                $table->integer(UserEmails::USER_ID)->comment('Идентификатор пользователя');
                $table->text('text')->comment('Текст сообщения');
                $table->timestamps();
                $table->softDeletes();
                $table->index(UserEmails::USER_ID, 'IDX_users_emails_user_id');

            });
            DB::statement("ALTER TABLE `users_emails` comment 'Письма отправленные пользователю администратором'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_emails');
    }
}
