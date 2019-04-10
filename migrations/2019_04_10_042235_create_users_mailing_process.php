<?php

use FastDog\User\Models\UserMailingProcess;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersMailingProcess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users_mailing_process')) {
            Schema::create('users_mailing_process', function (Blueprint $table) {
                $table->increments('id');

                $table->integer(UserMailingProcess::MAILING_ID)->nullable();
                $table->tinyInteger(UserMailingProcess::STATE)->default(UserMailingProcess::STATE_READY);
                $table->mediumInteger(UserMailingProcess::CURRENT_STEP)->default(0);

                $table->timestamps();
                $table->softDeletes();

                $table->index(UserMailingProcess::MAILING_ID, 'IDX_users_mailing_process_mailing_id');

            });
            DB::statement("ALTER TABLE `users_mailing_process` comment 'Лог выполнения рассылки'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_mailing_process');
    }
}
