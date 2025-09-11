<?php

namespace App\Traits;

trait Numbers
{
    /**
     * Round a number to a specified number of decimal places.
     *
     * @param float $number
     * @param int $decimalPlaces
     * @return float
     */
    protected function roundNumber($number, $decimalPlaces)
    {
        return number_format(round($number, $decimalPlaces), 2, '.', '.');
    }
}
