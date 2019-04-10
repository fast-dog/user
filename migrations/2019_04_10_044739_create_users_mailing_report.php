<?php

use FastDog\User\Models\UserMailingReport;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersMailingReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users_mailing_report')) {
            Schema::create('users_mailing_report', function (Blueprint $table) {
                $table->increments('id');
                $table->integer(UserMailingReport::USER_ID)->nullable(false);
                $table->integer(UserMailingReport::PROCESS_ID)->nullable(false);
                $table->unsignedInteger(UserMailingReport::MAILING_ID);
                $table->integer(UserMailingReport::TEMPLATE_ID)->nullable(false);
                $table->tinyInteger(UserMailingReport::STATE)->default(UserMailingReport::STATE_READY);
                $table->binary(UserMailingReport::DATA)->nullable(true);

                $table->timestamps();
                $table->softDeletes();

                $table->index(UserMailingReport::USER_ID, 'IDX_users_mailing_report_user_id');
                $table->index(UserMailingReport::PROCESS_ID, 'IDX_users_mailing_report_mailing_id');

            });

            Schema::table('users_mailing_report', function ($table) {
                $db = config('database.connections.mysql.database');
                $table->foreign(UserMailingReport::MAILING_ID, 'FK_users_mailing_report_mailing')
                    ->references('id')
                    ->on('users_mailing');
            });

            DB::statement("ALTER TABLE `users_mailing_report` comment 'Отчет выполнения рассылки'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('users_mailing_report')) {
            Schema::table('users_mailing_report', function ($table) {
                $table->dropForeign('FK_users_mailing_report_mailing');
            });
        }

        Schema::dropIfExists('users_mailing_report');
    }
}
