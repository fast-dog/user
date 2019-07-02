<?php

namespace FastDog\User\Console\Commands;


use FastDog\User\Events\UserRegistration;
use FastDog\User\Events\UserUpdate;
use FastDog\User\Models\User;
use Illuminate\Console\Command;

/**
 * Заполенение таблицы пользователей тестовыми данными
 *
 * @package App\Console\Commands
 */
class TestUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fill-test-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill 100 test users to db';


    /**
     * TestUsers constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //UserProfileCorporate::createDbSchema(); die;

        $faker = \Faker\Factory::create('Ru_RU');

        for ($i = 0; $i <= 500; $i++) {

            \Request::merge([
                'name' => $faker->firstName,
                'surname' => $faker->lastName,
                'email' => $faker->email,
                'password' => 'qwerty',
                'profile' => [
                    'data' => [
                        'about' => $faker->realText(150),
                    ],
                ],
            ]);
            $data = [
                User::EMAIL => \Request::input(User::EMAIL),
                User::TYPE => \Request::input(User::TYPE, User::USER_TYPE_USER),
                User::STATUS => \Request::input(User::STATUS, User::STATUS_ACTIVE),
                User::DATA => json_encode([
                    'profile' => [],
                ]),
                User::PASSWORD => \Hash::make(\Request::input(User::PASSWORD)),
            ];

            $user = User::create($data);

            event(new UserRegistration($user));

            event(new UserUpdate($user, app()->request));
        }
    }
}
