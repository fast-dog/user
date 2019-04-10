<?php

namespace FastDog\User\Providers;


use FastDog\User\Conversations\ConversationRepository;
use Illuminate\Container\Container;
use Nahid\Talk\Live\Broadcast;
use Nahid\Talk\Messages\MessageRepository;
use Nahid\Talk\Talk;
use Nahid\Talk\TalkServiceProvider as NahidTalkServiceProvider;

/**
 * Class Talk
 * @package FastDog\User\Providers
 */
class TalkServiceProvider extends NahidTalkServiceProvider
{

    /**
     * Register Talk class.
     */
    protected function registerTalk()
    {
        $this->app->singleton('talk', function (Container $app) {
            return new Talk($app['config'], new Broadcast($app['config']), $app[ConversationRepository::class],
                $app[MessageRepository::class]);
        });

        $this->app->alias('talk', Talk::class);
    }
}