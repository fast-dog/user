<?php

use FastDog\User\Models\Profile\UserProfileStat;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersProfileStat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users_profile_stat')) {
            Schema::create('users_profile_stat', function (Blueprint $table) {
                $table->increments('id');
                $table->integer(UserProfileStat::USER_ID);
                $table->integer(UserProfileStat::GUEST_ID);
                $table->timestamps();

                $table->index(UserProfileStat::USER_ID, 'IDX_user_profile_stat_user_id');
            });
            DB::statement("ALTER TABLE `users_profile_stat` comment 'Статситсика просмотра профиля'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_profile_stat');
    }
}
