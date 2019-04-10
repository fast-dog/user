<?php

use FastDog\User\Models\UserMailing;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersMailing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users_mailing')) {
            Schema::create('users_mailing', function (Blueprint $table) {
                $table->increments('id');

                $table->string(UserMailing::NAME)->nullable();
                $table->tinyInteger(UserMailing::STATE)->default(UserMailing::STATE_PUBLISHED);
                $table->string(UserMailing::SUBJECT)->nullable();
                $table->integer(UserMailing::TEMPLATE_ID)->nullable();
                $table->char(UserMailing::SITE_ID, 3)->default('001')->comment('Код сайта');
                $table->text(UserMailing::TEXT)->nullable();
                $table->dateTime(UserMailing::START_AT);

                $table->timestamps();
                $table->softDeletes();

                $table->index(UserMailing::SITE_ID, 'IDX_users_mailing_site_id');

            });
            DB::statement("ALTER TABLE `users_mailing` comment 'Рассылка по базе подписчиков'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_mailing');
    }
}
