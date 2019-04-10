<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersRegisterStat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users_register_stat')) {
            Schema::create('users_register_stat', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('value');
                $table->timestamps();
            });
            DB::statement("ALTER TABLE `users_register_stat` comment 'Статистика регистраций пользователей на сайте'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_register_stat');
    }
}
