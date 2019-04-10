<?php

use FastDog\User\Models\UserFavorites;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersFavorites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users_favorites')) {
            Schema::create('users_favorites', function (Blueprint $table) {
                $table->increments('id');
                $table->integer(UserFavorites::USER_ID)->nullable()->comment('Идентификатор владельца');
                $table->integer(UserFavorites::ITEM_ID)->nullable()->comment('Идентификатор пользователя');
                $table->timestamps();
                $table->softDeletes();

            });
            DB::statement("ALTER TABLE `users_favorites` comment 'Избранные пользователи'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_favorites');
    }
}
