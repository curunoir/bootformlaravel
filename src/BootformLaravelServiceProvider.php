<?php
namespace Dlouvard\Bootformlaravel;
/**
 * Created by PhpStorm.
 * User: dlouvard_imac
 * Date: 06/01/2017
 * Time: 22:28
 */
use Illuminate\Support\ServiceProvider;

class BootformLaravelServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('bootform', function ($app) {
            return new BootForm($app);
        });
    }

    public function provides()
    {
        return ['bootform'];
    }
}