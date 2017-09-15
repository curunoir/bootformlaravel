<?php

/**
 * Encodes data
 */
if (!function_exists('_c')) {
    function _c($data)
    {
        if (!is_array($data)) {
            return App::make('Hashids\Hashids')->encode($data);
        }
    }
}

/**
 *  Decodes Data
 */
if (!function_exists('_d')) {
    function _d($data)
    {
        $result = App::make('Hashids\Hashids')->decode($data);
        if (count($result) == 0) {
            abort('404');
        }
        if (count($result) == 1) {
            return $result[0];
        }
        return $result;
    }
}
