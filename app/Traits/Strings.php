<?php

namespace App\Traits;

trait Strings
{
    /**
     * Round a number to a specified number of decimal places.
     *
     * @param string $string
     * @param string $symbol_replace
     * @return string
     */
    protected function replaceEmpty($string, $symbol_replace)
    {
        if ($string == '' || $string == null) {
            return $symbol_replace;
        } else {
            return $string;
        }
    }
}
