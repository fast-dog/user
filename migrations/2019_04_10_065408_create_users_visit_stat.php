<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersVisitStat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users_visit_stat')) {
            Schema::create('users_visit_stat', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('value');
                $table->timestamps();
            });
            DB::statement("ALTER TABLE `users_visit_stat` comment 'Статистика посещений пользователями сайта'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_visit_stat');
    }
}
