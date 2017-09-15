# Bootformlaravel


## Installation

Require the translation package 

    composer require curunoir/bootformlaravel

Add the service provider to your `config/app.php` config file

    Vinkla\Hashids\HashidsServiceProvider::class,
    Dlouvard\Bootformlaravel\BootformLaravelServiceProvider::class,
    
Add the facade to your aliases in your `config/app.php` config file

    'Hashids' => Vinkla\Hashids\Facades\Hashids::class,
    'BootForm' => Dlouvard\Bootformlaravel\Facades\BootForm::class,
    
Publish the flags images assets

    php artisan vendor:publish --provider="Dlouvard\Bootformlaravel\BootformLaravelServiceProvider"
    

## Usage

In views

    BootForm::textLang('name','Nom',['required' => ''])


