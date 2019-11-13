<?php

use FastDog\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string(User::EMAIL, 80)->unique()->comment('email');
                $table->integer('orders')->default(0)->comment('КОл-во заказов');
                $table->string(User::PASSWORD, 60)->comment('Пароль');
                $table->string('remember_token', 60)->nullable();
                $table->enum(User::TYPE, [User::USER_TYPE_ADMIN, User::USER_TYPE_CORPORATE,
                    User::USER_TYPE_DEALER, User::USER_TYPE_USER])->default(User::USER_TYPE_USER)->comment('Тип аккаунта');
                $table->enum(User::STATUS, [User::STATUS_ACTIVE, User::STATUS_NOT_CONFIRMED,
                    User::STATUS_RESTORE_PASSWORD, User::STATUS_BANNED])->default(User::STATUS_NOT_CONFIRMED)
                    ->comment('Состояние');
                $table->json(User::DATA)->nullable()->comment('Дополнительные параметры');

                $table->char(User::SITE_ID, 3)->default('001')->comment('Код сайта');
                $table->enum(User::LANG, ['ru', 'en'])->comment('Код языка')->default('ru');
                $table->string(User::HASH, 32)->nullable();

                $table->index(User::SITE_ID, 'IDX_users_site_id');
                $table->timestamps();
                $table->softDeletes();
            });
            DB::statement("ALTER TABLE `users` comment 'Учетные записи пользователей'");

        }
        $user = User::create([
            User::EMAIL => 'admin@fastdog.ru',
            User::PASSWORD => \Hash::make('qwerty'),
            User::STATUS => User::STATUS_ACTIVE,
            User::TYPE => User::USER_TYPE_ADMIN,
            User::DATA => json_encode([]),
        ]);

        if (!Schema::hasTable('password_resets')) {
            Schema::create('password_resets', function (Blueprint $table) {
                $table->string('email')->index();
                $table->string('token')->index();
                $table->timestamps();
            });
            DB::statement("ALTER TABLE `password_resets` comment 'Восстановление пароля'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
