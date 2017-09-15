<?php
namespace Dlouvard\Bootformlaravel;

use Illuminate\Support\ServiceProvider;

class BootformLaravelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //AliasLoader::getInstance()->alias('BootForm',BootForm::class);
    }

    public function register()
    {
        $this->app->bind('bootform', function ($app) {
            return new BootForm($app['form'], $app['session']);
        });

        // Pictures for inputLang publishes in public/ressources/assets
        $this->publishes([
            __DIR__.'/ressources/assets/img/flags/' => base_path('/public/assets/img/vendor/flags/'),
        ], 'assets');

        // Include the helpers file for global hashid encoding `c` and 'd' functions
        include __DIR__.'/helpers_bootform.php';

    }

    public function provides()
    {
        return ['bootform'];
    }
}