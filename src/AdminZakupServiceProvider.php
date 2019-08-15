<?php

namespace Avl\AdminZakup;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Config;

class AdminZakupServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Публикуем файл конфигурации
        $this->publishes(
            [
                __DIR__ . '/../config/adminzakup.php' => config_path('adminzakup.php'),
            ]);

        $this->publishes(
            [
                __DIR__ . '/../public' => public_path('vendor/adminzakup'),
            ],
            'public');

        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'adminzakup');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Добавляем в глобальные настройки системы новый тип раздела
        Config::set('avl.sections.zakup', 'Закупки');

        // объединение настроек с опубликованной версией
        $this->mergeConfigFrom(__DIR__ . '/../config/adminzakup.php', 'adminzakup');

        // migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

    }

}
