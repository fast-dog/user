<?php

use FastDog\Core\Properties\BaseProperties;
use FastDog\User\Models\UserMailingTemplates;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersMailingTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users_mailing_templates')) {
            Schema::create('users_mailing_templates', function(Blueprint $table) {
                $table->increments('id');
                $table->string(UserMailingTemplates::NAME);
                $table->text(UserMailingTemplates::TEXT);
                $table->tinyInteger(UserMailingTemplates::STATE)->default(UserMailingTemplates::STATE_NOT_PUBLISHED);
                $table->char(UserMailingTemplates::SITE_ID, 3)->default('000');
                $table->timestamps();
                $table->softDeletes();

            });

            DB::statement("ALTER TABLE `users_mailing_templates` comment 'Шаблоны рассылок'");

            /** @var UserMailingTemplates $firstTemplate */

            $firstTemplate = UserMailingTemplates::create([
                UserMailingTemplates::NAME => trans('user::mailing.template.new.name'),
                UserMailingTemplates::TEXT => trans('user::mailing.template.new.html'),
                UserMailingTemplates::STATE => UserMailingTemplates::STATE_PUBLISHED,
                UserMailingTemplates::SITE_ID => '001',
            ]);

            /** @var \Illuminate\Support\Collection $properties */
            $properties = $firstTemplate->properties();

            $saveProperties = [];
            $fillProperties = [
                'FROM_ADDRESS' => config('mail.from.address'),
                'FROM_NAME' => config('mail.from.name'),
            ];
            $properties->each(function ($property) use (&$saveProperties, $fillProperties) {
                if (isset($fillProperties[$property[BaseProperties::ALIAS]])) {
                    $property[BaseProperties::VALUE] = $fillProperties[$property[BaseProperties::ALIAS]];
                    $property['show'] = true;// <- активное свойство
                    $saveProperties[] = $property;
                }
            });

            $firstTemplate->storeProperties(collect($saveProperties));
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_mailing_templates');
    }
}
