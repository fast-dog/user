<?php

namespace FastDog\User;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

/**
 * Class UserServiceProvider
 *
 * @package FastDog\User
 * @version 0.2.0
 * @author Андрей Мартынов <d.g.dev482@gmail.com>
 */
class UserServiceProvider extends LaravelServiceProvider
{
    const NAME = 'user';

    /**
     * php composer.phar update fast_dog_core:dev-master --prefer-source
     *
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->handleConfigs();
        $this->handleRoutes();
        $this->handleMigrations();
        $this->handleViews();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->register(UserEventServiceProvider::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * Определение конфигурации по умолчанию
     */
    private function handleConfigs(): void
    {
        $configPath = __DIR__ . '/../config/user.php';
        $this->publishes([$configPath => config_path('user.php')]);

        $this->mergeConfigFrom($configPath, self::NAME);
    }

    /**
     * Миграции базы данных
     */
    private function handleMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../migrations/');
    }


    /**
     * Определение маршрутов пакета
     */
    private function handleRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');
    }

    /**
     * Определение представлении пакета (шаблонов по умолчанию)
     */
    private function handleViews(): void
    {
        $this->publishes([__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR =>
            public_path('vendor/fast_dog/' . self::NAME)], 'public');
    }
}