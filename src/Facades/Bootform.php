<?php
namespace Dlouvard\Bootformlaravel\Facades;
/**
 * Created by PhpStorm.
 * User: dlouvard_imac
 * Date: 06/01/2017
 * Time: 22:32
 */

use \Illuminate\Support\Facades\Facade;

class BootForm extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'bootform';
    }
}
